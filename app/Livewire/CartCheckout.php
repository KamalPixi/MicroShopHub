<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderAddress;
// Assuming you have an OrderItem model, if not, create it.
use App\Models\OrderItem; 

class CartCheckout extends Component
{
    // Cart Data
    public $cart = [];
    
    // Totals
    public $subtotal = 0;
    public $discountAmount = 0;
    public $shippingCost = 0;
    public $total = 0;

    // Shipping & Coupon
    public $shippingMethods = [];
    public $selectedShippingMethod = null;
    public $couponCode = '';
    public $appliedCoupon = null;

    // Customer / Address Data
    public $email;
    public $phone;
    public $billing = [
        'name' => '',
        'address_line1' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => 'Bangladesh'
    ];
    public $shipping = [
        'name' => '',
        'address_line1' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => 'Bangladesh'
    ];
    public $shipToDifferentAddress = false;
    public $paymentMethod = 'cod'; // Default to Cash on Delivery

    protected $rules = [
        'email' => 'required|email',
        'phone' => 'required|string',
        'billing.name' => 'required|string',
        'billing.address_line1' => 'required|string',
        'billing.city' => 'required|string',
        'selectedShippingMethod' => 'required|exists:shipping_methods,id',
    ];

    public function mount()
    {
        $this->loadCart();
        $this->shippingMethods = ShippingMethod::where('active', true)->orderBy('cost')->get();
        
        // Default to first shipping method
        if($this->shippingMethods->isNotEmpty()) {
            $this->selectedShippingMethod = $this->shippingMethods->first()->id;
        }

        $this->calculateTotals();
    }

    public function loadCart()
    {
        $this->cart = Session::get('cart', []);
    }

    public function increment($key)
    {
        if(isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
            $this->updateSession();
        }
    }

    public function decrement($key)
    {
        if(isset($this->cart[$key]) && $this->cart[$key]['quantity'] > 1) {
            $this->cart[$key]['quantity']--;
            $this->updateSession();
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        $this->updateSession();
        $this->dispatch('cartUpdated'); // Update header counter
    }

    public function updateSession()
    {
        Session::put('cart', $this->cart);
        $this->calculateTotals();
        $this->dispatch('cartUpdated');
    }

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
            $this->addError('coupon', 'Invalid or expired coupon code.');
            $this->appliedCoupon = null;
        } else {
            // Check minimum spend
            if ($this->subtotal < $coupon->min_order_amount) {
                $this->addError('coupon', 'Minimum spend of $' . $coupon->min_order_amount . ' required.');
                return;
            }

            $this->appliedCoupon = $coupon;
            $this->couponCode = ''; // Clear input
            $this->dispatch('notify', ['message' => 'Coupon Applied!', 'type' => 'success']);
        }
        
        $this->calculateTotals();
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        // 1. Subtotal
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        // 2. Discount
        $this->discountAmount = 0;
        if ($this->appliedCoupon) {
            if ($this->appliedCoupon->type === 'percentage') {
                $this->discountAmount = ($this->subtotal * $this->appliedCoupon->value) / 100;
            } else {
                $this->discountAmount = $this->appliedCoupon->value;
            }
        }

        // 3. Shipping
        $method = $this->shippingMethods->find($this->selectedShippingMethod);
        $this->shippingCost = $method ? $method->cost : 0;

        // 4. Total
        $this->total = max(0, ($this->subtotal - $this->discountAmount) + $this->shippingCost);
    }

    public function placeOrder()
    {
        $this->validate();

        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        DB::transaction(function () {
            // 1. Create Order
            $order = Order::create([
                'user_id' => auth()->id(), // Nullable if guest checkout
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'shipping_cost' => $this->shippingCost,
                'discount' => $this->discountAmount,
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
                'shipping_method_id' => $this->selectedShippingMethod
            ]);

            // 2. Billing Address
            $order->addresses()->create(array_merge($this->billing, [
                'type' => 'billing',
                'email' => $this->email,
                'phone' => $this->phone
            ]));

            // 3. Shipping Address
            $shipData = $this->shipToDifferentAddress ? $this->shipping : $this->billing;
            $order->addresses()->create(array_merge($shipData, [
                'type' => 'shipping',
                'email' => $this->email,
                'phone' => $this->phone
            ]));

            // 4. Order Items
            foreach ($this->cart as $key => $item) {
                // If key contains hyphen, it has variation (prodId-varId)
                $ids = explode('-', $key); 
                $productId = $ids[0];
                $variationId = $ids[1] ?? null;

                // Assuming you have an OrderItem model setup
                // If not, you might need: DB::table('order_items')->insert(...)
                // Here is the Eloquent way:
                /* $order->items()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'attributes' => json_encode($item['attributes'] ?? []),
                ]);
                */
                
                // Decrement Stock
                $product = Product::find($productId);
                if($variationId && $product->has_variations) {
                     $var = $product->variations()->find($variationId);
                     if($var) $var->decrement('stock', $item['quantity']);
                } else {
                     $product->decrement('stock', $item['quantity']);
                }
            }
            
            // 5. Clear Session
            Session::forget('cart');
            $this->cart = [];
            $this->dispatch('cartUpdated');

            // 6. Redirect to Success (Create a route for this later)
            // return redirect()->route('order.success', $order->id);
            session()->flash('success', 'Order #' . $order->order_number . ' placed successfully!');
        });
        
        // Temporary feedback until success page exists
        $this->dispatch('notify', ['message' => 'Order Placed Successfully!', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.cart-checkout');
    }
}
