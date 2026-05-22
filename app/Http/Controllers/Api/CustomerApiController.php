<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\ShippingMethod;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\Product;
use App\Services\CustomerAuthService;
use App\Services\FlashSaleService;
use App\Jobs\SendAdminOrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerApiController extends Controller
{
    protected CustomerAuthService $authService;
    protected FlashSaleService $flashSaleService;

    public function __construct(CustomerAuthService $authService, FlashSaleService $flashSaleService)
    {
        $this->authService = $authService;
        $this->flashSaleService = $flashSaleService;
    }

    /**
     * Get authentication methods configurations.
     */
    public function authSettings()
    {
        return response()->json($this->authService->getAuthSettings());
    }

    /**
     * Send OTP to customer's email.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = strtolower(trim($request->input('email')));
        $this->authService->sendLoginOtp($email);

        return response()->json([
            'message' => 'A 6-digit verification code has been sent to your email.'
        ]);
    }

    /**
     * Login or create account via OTP.
     */
    public function loginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6'
        ]);

        $email = strtolower(trim($request->input('email')));
        $otp = trim($request->input('otp'));

        $user = $this->authService->loginWithOtp($email, $otp);

        if (! $user) {
            return response()->json([
                'message' => 'Invalid or expired verification code.'
            ], 422);
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        ]);
    }

    /**
     * Login via email & password.
     */
    public function loginPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember' => 'nullable|boolean'
        ]);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');
        $remember = filter_var($request->input('remember', false), FILTER_VALIDATE_BOOLEAN);

        if (! $this->authService->loginWithPassword($email, $password, $remember)) {
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 422);
        }

        $user = Auth::user();

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        ]);
    }

    /**
     * Register a new customer account.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $name = trim($request->input('name'));
        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        $user = $this->authService->registerWithPassword($name, $email, $password);

        if (! $user) {
            return response()->json([
                'message' => 'Registration failed. Email might already be taken.'
            ], 422);
        }

        return response()->json([
            'message' => 'Account registered and logged in successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        ]);
    }

    /**
     * Log out customer session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }

    /**
     * Get active logged-in customer info, address book, and recent order history.
     */
    public function currentUser()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $addresses = $user->addresses()->latest()->get();
        $orders = Order::where('user_id', $user->id)
            ->with(['items', 'addresses'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'subtotal' => (float) $order->subtotal,
                    'discount' => (float) $order->discount,
                    'shipping_cost' => (float) $order->shipping_cost,
                    'total' => (float) $order->total,
                    'currency_code' => $order->currency_code,
                    'created_at' => $order->created_at?->toIso8601String(),
                    'created_at_humans' => $order->created_at?->diffForHumans(),
                    'items' => $order->items->map(fn($item) => [
                        'name' => $item->name,
                        'price' => (float) $item->price,
                        'quantity' => (int) $item->quantity,
                        'attributes' => $item->attributes ?? [],
                    ]),
                    'shipping_address' => $order->addresses->firstWhere('type', 'shipping'),
                ];
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'addresses' => $addresses,
            'orders' => $orders
        ]);
    }

    /**
     * Check if a coupon is valid and return discount amount.
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $code = strtoupper(trim($request->input('code')));
        $subtotal = (float) $request->input('subtotal');

        $coupon = Discount::whereRaw('UPPER(code) = ?', [$code])
            ->where('active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (! $coupon) {
            return response()->json(['message' => 'Invalid or expired coupon.'], 422);
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            return response()->json(['message' => "Minimum spend of {$coupon->min_order_amount} required."], 422);
        }

        if ($coupon->usage_limit !== null && $coupon->orders()->count() >= $coupon->usage_limit) {
            return response()->json(['message' => 'This coupon usage limit has been reached.'], 422);
        }

        if (Auth::check() && $coupon->per_user_limit !== null) {
            $userUses = $coupon->orders()->where('user_id', Auth::id())->count();
            if ($userUses >= $coupon->per_user_limit) {
                return response()->json(['message' => 'You have already reached the limit for this coupon.'], 422);
            }
        }

        $discountAmount = 0;
        if ($coupon->type === 'percentage') {
            $discountAmount = ($subtotal * $coupon->value) / 100;
        } elseif ($coupon->type === 'fixed') {
            $discountAmount = $coupon->value;
        }

        $discountAmount = min($subtotal, (float) $discountAmount);

        return response()->json([
            'valid' => true,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
            'discount_amount' => round($discountAmount, 2),
        ]);
    }

    /**
     * Get options & configurations required for checkout page.
     */
    public function checkoutConfig()
    {
        $shippingMethods = ShippingMethod::where('active', true)->orderBy('cost', 'asc')->get()->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'cost' => (float) $m->cost,
            'description' => $m->description,
        ]);

        $supportedCountries = Country::where('active', true)->orderBy('name')->get(['code', 'name']);
        
        $settings = Setting::pluck('value', 'key')->toArray();
        $codEnabled = filter_var($settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $rawOffline = $settings['offline_payment_methods'] ?? '[]';
        $decodedOffline = is_string($rawOffline) ? json_decode($rawOffline, true) : $rawOffline;
        $offlinePaymentMethods = collect(is_array($decodedOffline) ? $decodedOffline : [])
            ->filter(fn ($method) => ! empty($method['active']))
            ->map(fn($method, $index) => [
                'id' => $index,
                'name' => $method['name'] ?? 'Offline Payment',
                'instructions' => $method['instructions'] ?? '',
            ])
            ->values()
            ->all();

        $savedAddresses = [];
        if (Auth::check()) {
            $savedAddresses = Auth::user()->addresses()->latest()->get();
        }

        return response()->json([
            'shipping_methods' => $shippingMethods,
            'supported_countries' => $supportedCountries,
            'cod_enabled' => $codEnabled,
            'offline_payment_methods' => $offlinePaymentMethods,
            'saved_addresses' => $savedAddresses,
        ]);
    }

    /**
     * Securely place an order from dynamic storefront request.
     */
    public function placeOrder(Request $request)
    {
        $authSettings = $this->authService->getAuthSettings();
        if (! Auth::check() && ! $authSettings['guest_checkout_enabled']) {
            return response()->json(['message' => 'Login is required to place an order.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'billing.name' => 'required|string|max:255',
            'billing.address_line1' => 'required|string|max:255',
            'billing.city' => 'required|string|max:100',
            'billing.country_code' => 'required|string|exists:countries,code',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|string|in:cod,offline',
            'offline_payment_method_id' => 'required_if:payment_method,offline',
            'offline_reference' => 'nullable|string|max:100',
            'offline_proof' => 'nullable|string', // Base64 or we can let them upload. But since it's an API, base64 or reference is better. We will accept base64.
            'coupon_code' => 'nullable|string',
            'ship_to_different_address' => 'required|boolean',
            'shipping.name' => 'required_if:ship_to_different_address,true|string|max:255',
            'shipping.address_line1' => 'required_if:ship_to_different_address,true|string|max:255',
            'shipping.city' => 'required_if:ship_to_different_address,true|string|max:100',
            'shipping.country_code' => 'required_if:ship_to_different_address,true|string|exists:countries,code',
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|integer',
            'cart.*.variation_id' => 'nullable|integer',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = $request->input('cart');
        $couponCode = $request->input('coupon_code');

        // Resolve active flash sale
        $activeFlashSale = $this->flashSaleService->currentSale();
        $flashSaleMap = $activeFlashSale ? $this->flashSaleService->productMap($activeFlashSale) : [];

        // 1. Calculate Prices on the dynamic DB state (safeguard against tampered pricing in request!)
        $subtotal = 0;
        $orderItemsData = [];

        foreach ($cart as $cartItem) {
            $product = Product::where('status', 1)->find($cartItem['product_id']);
            if (! $product) {
                return response()->json(['message' => 'One of the items in your cart is no longer available.'], 422);
            }

            $price = (float) $product->price;
            $stock = $product->stock;
            $variationId = null;

            if ($product->has_variations) {
                if (empty($cartItem['variation_id'])) {
                    return response()->json(['message' => "Please select options for {$product->name}."], 422);
                }
                $variation = $product->variations()->find($cartItem['variation_id']);
                if (! $variation) {
                    return response()->json(['message' => "Selected variation for {$product->name} does not exist."], 422);
                }
                $price = (float) $variation->price;
                $stock = $variation->stock;
                $variationId = $variation->id;
            }

            // Apply flash sale
            $resolvedPrice = $price;
            if ($activeFlashSale && isset($flashSaleMap[$product->id])) {
                $salePrice = $this->flashSaleService->applySale(
                    (float) $price,
                    (string) $activeFlashSale->sale_type,
                    (float) $activeFlashSale->sale_value
                );
                $salePrice = max(0, round($salePrice, 2));
                if ($salePrice < $price) {
                    $resolvedPrice = $salePrice;
                }
            }

            // Stock Check
            if ($stock < $cartItem['quantity']) {
                return response()->json(['message' => "Only {$stock} units of {$product->name} are available."], 422);
            }

            $subtotal += $resolvedPrice * $cartItem['quantity'];

            // Save for creation later
            $orderItemsData[] = [
                'product' => $product,
                'variation_id' => $variationId,
                'name' => $product->name,
                'price' => $resolvedPrice,
                'quantity' => (int) $cartItem['quantity'],
                'attributes' => $cartItem['attributes'] ?? []
            ];
        }

        // 2. Validate Coupon Code
        $coupon = null;
        $discountAmount = 0;
        if (! empty($couponCode)) {
            $code = strtoupper(trim($couponCode));
            $coupon = Discount::whereRaw('UPPER(code) = ?', [$code])
                ->where('active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->first();

            if (! $coupon) {
                return response()->json(['message' => 'Invalid or expired coupon code.'], 422);
            }

            if ($subtotal < (float) $coupon->min_order_amount) {
                return response()->json(['message' => "Minimum spend of {$coupon->min_order_amount} required for this coupon."], 422);
            }

            if ($coupon->usage_limit !== null && $coupon->orders()->count() >= $coupon->usage_limit) {
                return response()->json(['message' => 'This coupon limit has been reached.'], 422);
            }

            if (Auth::check() && $coupon->per_user_limit !== null) {
                $userUses = $coupon->orders()->where('user_id', Auth::id())->count();
                if ($userUses >= $coupon->per_user_limit) {
                    return response()->json(['message' => 'You have already reached the limit for this coupon.'], 422);
                }
            }

            if ($coupon->type === 'percentage') {
                $discountAmount = ($subtotal * $coupon->value) / 100;
            } elseif ($coupon->type === 'fixed') {
                $discountAmount = $coupon->value;
            }

            $discountAmount = min($subtotal, (float) $discountAmount);
        }

        // 3. Resolve Shipping cost
        $shippingMethod = ShippingMethod::where('active', true)->find($request->input('shipping_method_id'));
        if (! $shippingMethod) {
            return response()->json(['message' => 'Selected shipping method is not available.'], 422);
        }

        $shippingCost = (float) $shippingMethod->cost;
        if ($coupon && $coupon->type === 'free_shipping') {
            $shippingCost = 0;
        }

        $total = max(0, ($subtotal - $discountAmount) + $shippingCost);

        // 4. Save Order inside secure DB transaction
        $order = DB::transaction(function () use ($request, $subtotal, $discountAmount, $shippingCost, $total, $coupon, $orderItemsData) {
            $currency = Currency::getActive();
            
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'currency_code' => $currency?->code ?? 'BDT',
                'exchange_rate' => $currency?->exchange_rate ?? 1.0000,
                'shipping_method_id' => $request->input('shipping_method_id'),
                'payment_method' => $request->input('payment_method'),
                'payment_status' => $request->input('payment_method') === 'offline' ? 'pending_verification' : 'pending',
            ]);

            if ($coupon) {
                $order->discounts()->attach($coupon->id, [
                    'applied_value' => $discountAmount,
                ]);
            }

            // Create Addresses
            $billingAddress = $request->input('billing');
            $order->addresses()->create(array_merge([
                'name' => $billingAddress['name'],
                'address_line1' => $billingAddress['address_line1'],
                'address_line2' => $billingAddress['address_line2'] ?? null,
                'city' => $billingAddress['city'],
                'state' => $billingAddress['state'] ?? null,
                'postal_code' => $billingAddress['postal_code'] ?? null,
                'country_code' => $billingAddress['country_code'],
                'type' => 'billing',
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]));

            $shipToDiff = filter_var($request->input('ship_to_different_address'), FILTER_VALIDATE_BOOLEAN);
            $shippingAddress = $shipToDiff ? $request->input('shipping') : $billingAddress;
            $order->addresses()->create(array_merge([
                'name' => $shippingAddress['name'],
                'address_line1' => $shippingAddress['address_line1'],
                'address_line2' => $shippingAddress['address_line2'] ?? null,
                'city' => $shippingAddress['city'],
                'state' => $shippingAddress['state'] ?? null,
                'postal_code' => $shippingAddress['postal_code'] ?? null,
                'country_code' => $shippingAddress['country_code'],
                'type' => 'shipping',
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]));

            // Create OrderItems and Deduct Inventory
            foreach ($orderItemsData as $itemData) {
                $product = $itemData['product'];
                $varId = $itemData['variation_id'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variation_id' => $varId,
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'attributes' => $itemData['attributes'] ?? []
                ]);

                // Inventory decrement
                if ($varId) {
                    $var = $product->variations()->find($varId);
                    if ($var) {
                        $var->decrement('stock', $itemData['quantity']);
                    }
                } else {
                    $product->decrement('stock', $itemData['quantity']);
                }
            }

            // Process Offline Payments
            if ($request->input('payment_method') === 'offline') {
                $settings = Setting::pluck('value', 'key')->toArray();
                $rawOffline = $settings['offline_payment_methods'] ?? '[]';
                $decodedOffline = is_string($rawOffline) ? json_decode($rawOffline, true) : $rawOffline;
                $offlineMethods = is_array($decodedOffline) ? $decodedOffline : [];

                $methodIndex = (int) $request->input('offline_payment_method_id');
                $selectedMethod = $offlineMethods[$methodIndex] ?? null;

                $attachmentPath = null;
                // If proof uploaded as Base64 string
                $proofBase64 = $request->input('offline_proof');
                if (! empty($proofBase64) && preg_match('/^data:(\w+\/\w+);base64,/', $proofBase64, $typeMatch)) {
                    $fileType = $typeMatch[1];
                    $extension = match ($fileType) {
                        'image/jpeg', 'image/jpg' => 'jpg',
                        'image/png' => 'png',
                        'application/pdf' => 'pdf',
                        default => null
                    };

                    if ($extension) {
                        $data = substr($proofBase64, strpos($proofBase64, ',') + 1);
                        $data = base64_decode($data);
                        
                        $filename = 'offline-payments/' . uniqid() . '.' . $extension;
                        Storage::put($filename, $data);
                        $attachmentPath = $filename;
                    }
                }

                $order->offlinePayments()->create([
                    'method_name' => $selectedMethod['name'] ?? 'Offline Payment',
                    'instructions' => $selectedMethod['instructions'] ?? null,
                    'reference' => $request->input('offline_reference') ?: null,
                    'amount' => $total,
                    'attachment_path' => $attachmentPath,
                    'status' => 'pending',
                ]);
            }

            return $order;
        });

        // Trigger notifications
        $settings = Setting::whereIn('key', ['admin_notify_email_enabled', 'admin_notify_telegram_enabled'])
            ->pluck('value', 'key')
            ->toArray();
        $emailEnabled = filter_var($settings['admin_notify_email_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $telegramEnabled = filter_var($settings['admin_notify_telegram_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($emailEnabled || $telegramEnabled) {
            SendAdminOrderNotification::dispatch($order->id)->afterCommit();
        }

        return response()->json([
            'message' => 'Order placed successfully!',
            'order_number' => $order->order_number,
            'total' => (float) $order->total,
        ]);
    }
}
