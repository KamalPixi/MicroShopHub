<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class PaymentController extends Controller
{
    // --- 1. INITIATE PAYMENT ---
    public function pay(Request $request)
    {
        // Validate request has a gateway
        $request->validate(['gateway' => 'required|string']);
        $gateway = $request->input('gateway');

        // Route to the specific gateway logic
        switch ($gateway) {
            case 'sslcommerz':
                return $this->payWithSslCommerz($request);
            
            case 'stripe':
                // return $this->payWithStripe($request); // Future implementation
                return redirect()->back()->with('error', 'Stripe not implemented yet.');

            default:
                return redirect()->back()->with('error', 'Invalid Payment Gateway selected.');
        }
    }

    // --- 2. GATEWAY SPECIFIC LOGIC (SSLCommerz) ---
    
    private function payWithSslCommerz(Request $request)
    {
        // Fetch Settings
        $settings = Setting::whereIn('key', ['sslcommerz_store_id', 'sslcommerz_api_key', 'sslcommerz_sandbox'])->pluck('value', 'key');
        
        $store_id = $settings['sslcommerz_store_id'] ?? null;
        $store_passwd = $settings['sslcommerz_api_key'] ?? null;
        $is_sandbox = filter_var($settings['sslcommerz_sandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        if (!$store_id || !$store_passwd) {
            return redirect()->back()->with('error', 'SSLCommerz credentials missing.');
        }

        $api_url = $is_sandbox 
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' 
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $post_data = [
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'total_amount' => '100', // Replace with dynamic amount
            'currency' => 'BDT',
            'tran_id' => "TRX-" . uniqid(),
            
            // DYNAMIC CALLBACK URLs (Using the global route names)
            'success_url' => route('payment.success', ['gateway' => 'sslcommerz']),
            'fail_url'    => route('payment.fail', ['gateway' => 'sslcommerz']),
            'cancel_url'  => route('payment.cancel', ['gateway' => 'sslcommerz']),
            'ipn_url'     => route('payment.ipn', ['gateway' => 'sslcommerz']),

            // Customer Info (Dummy Data)
            'cus_name' => 'John Doe',
            'cus_email' => 'john@example.com',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01711111111',
            
            'shipping_method' => 'NO',
            'product_name' => 'Online Goods',
            'product_category' => 'General',
            'product_profile' => 'general',
        ];

        $response = Http::asForm()->post($api_url, $post_data);
        $result = $response->json();

        if (isset($result['status']) && $result['status'] == 'SUCCESS') {
            return redirect($result['GatewayPageURL']);
        } else {
            return redirect()->back()->with('error', 'SSLCommerz Error: ' . ($result['failedreason'] ?? 'Unknown error'));
        }
    }

    // --- 3. GLOBAL CALLBACK HANDLERS ---

    public function success($gateway, Request $request)
    {
        if ($gateway == 'sslcommerz') {
            // Validate SSLCommerz Transaction
            $tran_id = $request->input('tran_id');
            // Update Database Logic Here...
            
            return redirect()->route('store.index')->with('success', "Payment Successful via SSLCommerz! TRX: $tran_id");
        }

        // Add 'elseif ($gateway == 'stripe')' later...

        return redirect()->route('store.index')->with('error', 'Unknown Payment Gateway.');
    }

    public function fail($gateway, Request $request)
    {
        return redirect()->route('store.index')->with('error', ucfirst($gateway) . ' Payment Failed.');
    }

    public function cancel($gateway, Request $request)
    {
        return redirect()->route('store.index')->with('warning', ucfirst($gateway) . ' Payment Cancelled.');
    }

    public function ipn($gateway, Request $request)
    {
        // Log the incoming request for debugging
        Log::info("IPN Received from {$gateway}:", $request->all());

        switch ($gateway) {
            case 'sslcommerz':
                return $this->handleSslCommerzIpn($request);
            
            case 'stripe':
                // return $this->handleStripeWebhook($request);
                return response()->json(['status' => 'Stripe not implemented'], 404);

            default:
                return response()->json(['status' => 'Unknown Gateway'], 400);
        }
    }

    private function handleSslCommerzIpn(Request $request)
    {
        // 1. Get Credentials
        $settings = Setting::whereIn('key', ['sslcommerz_store_id', 'sslcommerz_api_key', 'sslcommerz_sandbox'])->pluck('value', 'key');
        $store_id = $settings['sslcommerz_store_id'];
        $store_passwd = $settings['sslcommerz_api_key'];
        $is_sandbox = filter_var($settings['sslcommerz_sandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // 2. Check if Transaction Exists & is Valid
        // SSLCommerz sends 'tran_id' and 'val_id' in the POST request
        $tran_id = $request->input('tran_id');
        $val_id = $request->input('val_id');
        $amount = $request->input('amount');

        if (!$tran_id || !$val_id) {
            return response()->json(['status' => 'Invalid Data'], 400);
        }

        // 3. VERIFY WITH SSLCOMMERZ API (Double Check)
        // Never trust the request data alone; ask SSLCommerz if this is real.
        $validator_url = $is_sandbox 
            ? "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php"
            : "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php";

        $response = Http::get($validator_url, [
            'val_id' => $val_id,
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'format' => 'json'
        ]);

        $result = $response->json();

        if ($result['status'] === 'VALID' || $result['status'] === 'VALIDATED') {
            
            // 4. Find & Update Order
            // Assuming you saved 'transaction_id' in your orders table when initiating payment
            $order = Order::where('transaction_id', $tran_id)->first();

            if ($order) {
                // Security Check: Verify Amount Matches
                if ((float) $order->grand_total !== (float) $amount) {
                    Log::error("SSLCommerz Fraud Attempt: Amount mismatch for Order #{$order->id}");
                    return response()->json(['status' => 'Fraud Detected'], 400);
                }

                // Update Order Status
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing', // or 'completed'
                        'payment_details' => json_encode($result) // Save full gateway response for audit
                    ]);
                    
                    Log::info("Order #{$order->id} marked as PAID via SSLCommerz IPN.");
                }
                
                return response()->json(['status' => 'IPN Processed Successfully']);
            }
        }

        return response()->json(['status' => 'Validation Failed'], 400);
    }
}
