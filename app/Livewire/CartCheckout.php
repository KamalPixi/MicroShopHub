<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Discount;
use App\Models\Order;
use App\Models\User;

class CartCheckout extends Component
{
    // --- Cart Data ---
    public $cart = [];
    public $subtotal = 0;
    public $discountAmount = 0;
    public $shippingCost = 0;
    public $total = 0;

    // --- Configuration ---
    public $shippingMethods = [];
    public $selectedShippingMethod = null;
    public $couponCode = '';
    public $appliedCoupon = null;
    public $paymentMethod = 'cod'; // Default payment gateway code

    // --- Authentication (OTP) ---
    public $email = '';
    public $phone = '';
    public $otp = '';
    public $otpSent = false;
    
    // --- Addresses ---
    public $shipToDifferentAddress = false;
    public $billing = [
        'name' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country_code' => 'BD', // Default to Bangladesh
    ];
    public $shipping = [
        'name' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country_code' => 'BD',
    ];

    // --- Validation Rules ---
    protected function rules()
    {
        return [
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'billing.name' => 'required|string|max:255',
            'billing.address_line1' => 'required|string|max:255',
            'billing.city' => 'required|string|max:100',
            'billing.country_code' => 'required|string|size:2',
            'selectedShippingMethod' => 'required|exists:shipping_methods,id',
        ];
    }

    public function mount()
    {
        $this->cart = Session::get('cart', []);
        
        // Load active shipping methods (In a real app, filter this by Zone based on country)
        $this->shippingMethods = ShippingMethod::where('active', true)->orderBy('cost', 'asc')->get();
        
        // Default selection
        if ($this->shippingMethods->isNotEmpty()) {
            $this->selectedShippingMethod = $this->shippingMethods->first()->id;
        }

        // Auto-fill if logged in
        if (Auth::check()) {
            $user = Auth::user();
            $this->email = $user->email;
            $this->billing['name'] = $user->name;
            $this->phone = $user->phone ?? ''; // Assuming user model has phone
        }

        $this->calculateTotals();
    }

    // ==========================================
    // 1. Authentication Logic (OTP)
    // ==========================================

    public function sendOtp()
    {
        $this->validate(['email' => 'required|email']);
        
        $token = rand(100000, 999999);
        
        // Store OTP
        DB::table('otps')->updateOrInsert(
            ['identifier' => $this->email],
            [
                'token' => $token,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // TODO: Send real email here using Mail::to($this->email)...
        Log::info("OTP for {$this->email}: {$token}"); // Check laravel.log for code

        $this->otpSent = true;
        session()->flash('otp_message', 'We sent a 6-digit code to your email.');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);

        $record = DB::table('otps')
            ->where('identifier', $this->email)
            ->where('token', $this->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            $this->addError('otp', 'Invalid or expired code.');
            return;
        }

        // Find or Create User
        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => 'Guest Customer',
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);

        // Cleanup & Reset UI
        DB::table('otps')->where('identifier', $this->email)->delete();
        $this->otpSent = false;
        $this->otp = '';
        
        // Auto-fill billing name if needed
        if (empty($this->billing['name'])) {
            $this->billing['name'] = $user->name;
        }

        session()->flash('auth_success', 'Login successful!');
    }

    // ==========================================
    // 2. Cart Management
    // ==========================================

    public function increment($key)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
            $this->updateSession();
        }
    }

    public function decrement($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] > 1) {
                $this->cart[$key]['quantity']--;
            } else {
                unset($this->cart[$key]); // Remove if qty becomes 0
            }
            $this->updateSession();
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        $this->updateSession();
    }

    public function updateSession()
    {
        Session::put('cart', $this->cart);
        $this->calculateTotals();
        $this->dispatch('cartUpdated'); // Updates header badge
    }

    // ==========================================
    // 3. Totals & Coupons
    // ==========================================

    public function updatedSelectedShippingMethod()
    {
        $this->calculateTotals();
    }

    public function applyCoupon()
    {
        $this->resetErrorBag('coupon');
        if (empty($this->couponCode)) return;

        $coupon = Discount::where('code', $this->couponCode)
            ->where('active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->first();

        if (!$coupon) {
            $this->addError('coupon', 'Invalid or expired coupon.');
            return;
        }

        if ($this->subtotal < $coupon->min_order_amount) {
            $this->addError('coupon', "Minimum spend of {$coupon->min_order_amount} required.");
            return;
        }

        $this->appliedCoupon = $coupon;
        $this->couponCode = '';
        $this->calculateTotals();
        session()->flash('coupon_success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->discountAmount = 0;
        if ($this->appliedCoupon) {
            if ($this->appliedCoupon->type === 'percentage') {
                $this->discountAmount = ($this->subtotal * $this->appliedCoupon->value) / 100;
            } else {
                $this->discountAmount = $this->appliedCoupon->value;
            }
        }

        $method = $this->shippingMethods->find($this->selectedShippingMethod);
        $this->shippingCost = $method ? $method->cost : 0;

        $this->total = max(0, ($this->subtotal - $this->discountAmount) + $this->shippingCost);
    }

    // ==========================================
    // 4. Checkout Logic
    // ==========================================

    public function placeOrder()
    {
        // 1. Validation
        if (!Auth::check()) {
            $this->addError('email', 'Please login or verify email to place an order.');
            return;
        }

        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            // 2. Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                
                // Financials
                'subtotal' => $this->subtotal,
                'discount' => $this->discountAmount,
                'shipping_cost' => $this->shippingCost,
                'total' => $this->total,
                
                // Global Settings (Snapshots)
                'currency_code' => 'USD', // Replace with dynamic currency if implemented
                'exchange_rate' => 1.0000,
                
                // Methods
                'shipping_method_id' => $this->selectedShippingMethod,
                'payment_method' => $this->paymentMethod, // 'cod', 'stripe', etc.
                'payment_status' => 'pending',
            ]);

            // 3. Save Polymorphic Addresses
            
            // Billing
            $order->addresses()->create(array_merge($this->billing, [
                'type' => 'billing',
                'email' => $this->email,
                'phone' => $this->phone
            ]));

            // Shipping
            $shipData = $this->shipToDifferentAddress ? $this->shipping : $this->billing;
            $order->addresses()->create(array_merge($shipData, [
                'type' => 'shipping',
                'email' => $this->email,
                'phone' => $this->phone
            ]));

            // 4. Save Order Items & Update Stock
            foreach ($this->cart as $key => $item) {
                // Parse ID and Variation from Key "1-2" (Product 1, Variation 2)
                $parts = explode('-', $key);
                $productId = $parts[0];
                $variationId = $parts[1] ?? null;

                // Create Item (Assuming you have an OrderItem model)
                // If not using model: DB::table('order_items')->insert(...)
                /*
                $order->items()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'attributes' => json_encode($item['attributes'] ?? []),
                ]);
                */

                // Stock Management
                $product = Product::find($productId);
                if ($product) {
                    if ($variationId && $product->has_variations) {
                        $var = $product->variations()->find($variationId);
                        if ($var) $var->decrement('stock', $item['quantity']);
                    } else {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }

            // 5. Clear Cart & Finish
            Session::forget('cart');
            $this->cart = [];
            $this->dispatch('cartUpdated');
            
            session()->flash('order_success', "Order #{$order->order_number} placed successfully!");
            
            // Optional: Redirect to success page
            // return redirect()->route('order.success', $order->id);
        });
    }

    public function render()
    {
        return view('livewire.cart-checkout');
    }
}
