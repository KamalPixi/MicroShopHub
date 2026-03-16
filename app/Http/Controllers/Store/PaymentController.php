<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Models\Order;
use App\Models\Currency; // [NEW] Import Currency Model

class PaymentController extends Controller
{
    // --- 1. INITIATE PAYMENT ---
    public function pay(Request $request)
    {
        $request->validate(['gateway' => 'required|string']);
        $gateway = $request->input('gateway');

        switch ($gateway) {
            case 'sslcommerz':
                return $this->payWithSslCommerz($request);
            
            case 'cod': // Handle COD
                return $this->processCod($request);

            case 'bkash':
                return $this->payWithBkash($request);

            case 'stripe':
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

        //  Get Store Currency dynamically
        $currencyCode = Currency::getActive()->code; 

        $api_url = $is_sandbox 
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' 
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $post_data = [
            'store_id' => $store_id,
            'store_passwd' => $store_passwd,
            'total_amount' => '100', // Replace with actual Cart Total in production
            'currency' => $currencyCode, // [UPDATED] Use dynamic store currency
            'tran_id' => "TRX-" . uniqid(),
            
            // DYNAMIC CALLBACK URLs
            'success_url' => route('payment.success', ['gateway' => 'sslcommerz']),
            'fail_url'    => route('payment.fail', ['gateway' => 'sslcommerz']),
            'cancel_url'  => route('payment.cancel', ['gateway' => 'sslcommerz']),
            'ipn_url'     => route('payment.ipn', ['gateway' => 'sslcommerz']),

            // Customer Info (Dummy Data - Connect to Auth::user() later)
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

    // --- 3. HANDLE CASH ON DELIVERY ---
    private function processCod(Request $request)
    {
        // Logic to create order with 'pending' payment status
        // Order::create([... 'payment_method' => 'cod', 'payment_status' => 'pending' ...]);

        return redirect()->route('store.index')->with('success', 'Order placed successfully via Cash on Delivery!');
    }

    // --- 3.5. GATEWAY SPECIFIC LOGIC (bKash Checkout URL) ---
    private function payWithBkash(Request $request)
    {
        $settings = Setting::whereIn('key', [
            'bkash_base_url',
            'bkash_app_key',
            'bkash_app_secret',
            'bkash_username',
            'bkash_password',
        ])->pluck('value', 'key');

        $baseUrl = rtrim((string) ($settings['bkash_base_url'] ?? ''), '/');
        $appKey = $settings['bkash_app_key'] ?? null;
        $appSecret = $settings['bkash_app_secret'] ?? null;
        $username = $settings['bkash_username'] ?? null;
        $password = $settings['bkash_password'] ?? null;

        if (! $baseUrl || ! $appKey || ! $appSecret || ! $username || ! $password) {
            return redirect()->back()->with('error', 'bKash credentials are missing.');
        }

        $currencyCode = Currency::getActive()->code;
        $amount = (string) $request->input('amount', 0);
        $payerReference = (string) $request->input('payer_reference', 'customer');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Invalid payment amount.');
        }

        $tokenUrl = $baseUrl . '/tokenized/checkout/token/grant';
        $createUrl = $baseUrl . '/tokenized/checkout/create';

        $tokenResponse = Http::withHeaders([
            'username' => $username,
            'password' => $password,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($tokenUrl, [
            'app_key' => $appKey,
            'app_secret' => $appSecret,
        ]);

        $tokenData = $tokenResponse->json();
        $idToken = $tokenData['id_token'] ?? null;

        if (! $idToken) {
            return redirect()->back()->with('error', 'bKash token grant failed.');
        }

        $createResponse = Http::withHeaders([
            'Authorization' => $idToken,
            'X-APP-Key' => $appKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($createUrl, [
            'mode' => '0011',
            'payerReference' => $payerReference,
            'callbackURL' => route('payment.bkash.callback'),
            'amount' => $amount,
            'currency' => $currencyCode,
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'INV-' . strtoupper(uniqid()),
        ]);

        $createData = $createResponse->json();
        $bkashUrl = $createData['bkashURL'] ?? null;
        $paymentId = $createData['paymentID'] ?? null;

        if (! $bkashUrl || ! $paymentId) {
            return redirect()->back()->with('error', 'bKash payment creation failed.');
        }

        session()->put("bkash.payment.{$paymentId}", [
            'id_token' => $idToken,
            'app_key' => $appKey,
            'base_url' => $baseUrl,
        ]);

        return redirect()->away($bkashUrl);
    }

    // --- 4. GLOBAL CALLBACK HANDLERS ---

    public function success($gateway, Request $request)
    {
        if ($gateway == 'sslcommerz') {
            $tran_id = $request->input('tran_id');
            // Update Database Logic Here...
            return redirect()->route('store.index')->with('success', "Payment Successful via SSLCommerz! TRX: $tran_id");
        }

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
        Log::info("IPN Received from {$gateway}:", $request->all());

        switch ($gateway) {
            case 'sslcommerz':
                return $this->handleSslCommerzIpn($request);
            
            case 'stripe':
                return response()->json(['status' => 'Stripe not implemented'], 404);

            default:
                return response()->json(['status' => 'Unknown Gateway'], 400);
        }
    }

    private function handleSslCommerzIpn(Request $request)
    {
        $settings = Setting::whereIn('key', ['sslcommerz_store_id', 'sslcommerz_api_key', 'sslcommerz_sandbox'])->pluck('value', 'key');
        $store_id = $settings['sslcommerz_store_id'];
        $store_passwd = $settings['sslcommerz_api_key'];
        $is_sandbox = filter_var($settings['sslcommerz_sandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $tran_id = $request->input('tran_id');
        $val_id = $request->input('val_id');
        $amount = $request->input('amount');

        if (!$tran_id || !$val_id) {
            return response()->json(['status' => 'Invalid Data'], 400);
        }

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
            
            $order = Order::where('transaction_id', $tran_id)->first();

            if ($order) {
                if ((float) $order->grand_total !== (float) $amount) {
                    Log::error("SSLCommerz Fraud Attempt: Amount mismatch for Order #{$order->id}");
                    return response()->json(['status' => 'Fraud Detected'], 400);
                }

                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'payment_details' => json_encode($result)
                    ]);
                    Log::info("Order #{$order->id} marked as PAID via SSLCommerz IPN.");
                }
                
                return response()->json(['status' => 'IPN Processed Successfully']);
            }
        }

        return response()->json(['status' => 'Validation Failed'], 400);
    }

    // --- 5. bKash Callback ---
    public function bkashCallback(Request $request)
    {
        $paymentId = $request->input('paymentID');
        $status = strtolower((string) $request->input('status', ''));

        if (! $paymentId) {
            return redirect()->route('store.index')->with('error', 'Invalid bKash callback.');
        }

        if ($status !== 'success') {
            return redirect()->route('store.index')->with('warning', 'bKash payment cancelled or failed.');
        }

        $sessionKey = "bkash.payment.{$paymentId}";
        $paymentSession = session()->get($sessionKey);
        if (! $paymentSession) {
            return redirect()->route('store.index')->with('error', 'bKash session expired.');
        }

        $executeUrl = rtrim($paymentSession['base_url'], '/') . '/tokenized/checkout/execute';
        $executeResponse = Http::withHeaders([
            'Authorization' => $paymentSession['id_token'],
            'X-APP-Key' => $paymentSession['app_key'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($executeUrl, [
            'paymentID' => $paymentId,
        ]);

        $executeData = $executeResponse->json();
        $statusCode = $executeData['statusCode'] ?? null;

        session()->forget($sessionKey);

        if ($statusCode === '0000') {
            return redirect()->route('store.index')->with('success', 'bKash payment successful.');
        }

        return redirect()->route('store.index')->with('error', 'bKash payment execution failed.');
    }
}
