<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Setting;

class SslCommerzController extends Controller
{
    private $store_id;
    private $store_password;
    private $api_url;

    public function __construct()
    {
        // Load credentials from DB
        $settings = Setting::whereIn('key', ['sslcommerz_store_id', 'sslcommerz_api_key', 'sslcommerz_sandbox'])->pluck('value', 'key');
        
        $this->store_id = $settings['sslcommerz_store_id'] ?? null;
        $this->store_password = $settings['sslcommerz_api_key'] ?? null;
        $is_sandbox = filter_var($settings['sslcommerz_sandbox'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $this->api_url = $is_sandbox 
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' 
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
    }

    public function index(Request $request)
    {

        // 1. Validate Cart/Order
        // Assume you pass an order_id or calculate total from cart session
        $total_amount = 100; // Replace with actual cart total
        $transaction_id = uniqid(); // Generate unique tran_id

        // 2. Prepare Data
        $post_data = [
            'store_id' => $this->store_id,
            'store_passwd' => $this->store_password,
            'total_amount' => $total_amount,
            'currency' => 'BDT',
            'tran_id' => $transaction_id,
            'success_url' => route('ssl.success'),
            'fail_url' => route('ssl.fail'),
            'cancel_url' => route('ssl.cancel'),
            'ipn_url' => route('ssl.ipn'),
            
            // Customer Info (Replace with actual user data)
            'cus_name' => 'Customer Name',
            'cus_email' => 'cust@yahoo.com',
            'cus_add1' => 'Dhaka',
            'cus_add2' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_state' => 'Dhaka',
            'cus_postcode' => '1000',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01711111111',
            'cus_fax' => '01711111111',
            
            // Product Info
            'shipping_method' => 'NO',
            'product_name' => 'Computer',
            'product_category' => 'Goods',
            'product_profile' => 'physical-goods',
        ];

        // 3. Send Request to SSLCommerz
        $response = Http::asForm()->post($this->api_url, $post_data);
        $result = $response->json();

        dd($result);

        if (isset($result['status']) && $result['status'] == 'SUCCESS') {
            // Save transaction_id to your Order model here before redirecting
            // Order::create(['transaction_id' => $transaction_id, ...]);
            
            return redirect($result['GatewayPageURL']);
        } else {
            return redirect()->back()->with('error', 'Configuration error or API connection failed.');
        }
    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        // VALIDATE THE TRANSACTION
        // It's strictly recommended to verify the transaction status again using the Order Validation API
        
        // 1. Update Order Status in Database
        // $order = Order::where('transaction_id', $tran_id)->first();
        // $order->update(['status' => 'processing', 'payment_status' => 'paid']);

        return redirect()->route('store.index')->with('success', 'Payment Successful! Transaction ID: ' . $tran_id);
    }

    public function fail(Request $request)
    {
        return redirect()->route('store.index')->with('error', 'Payment Failed. Please try again.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('store.index')->with('warning', 'Payment Cancelled.');
    }

    public function ipn(Request $request)
    {
        // Handle Instant Payment Notification (Background check)
        // Verify transaction and update database if user closed browser before success page
    }
}
