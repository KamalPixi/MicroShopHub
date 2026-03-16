<?php

namespace App\Livewire\Store;

use App\Models\Currency;
use App\Models\Country;
use App\Models\Discount;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CustomerAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CartCheckout extends Component
{
    protected CartService $cartService;
    protected CustomerAuthService $customerAuthService;

    public $cart = [];
    public $currencySymbol = '$';
    public $subtotal = 0;
    public $discountAmount = 0;
    public $shippingCost = 0;
    public $total = 0;

    public $savedAddresses = [];
    public $selectedAddressId = null;

    public $shippingMethods = [];
    public $selectedShippingMethod = null;
    public $couponCode = '';
    public $appliedCoupon = null;
    public $paymentMethod = 'cod';

    public $email = '';
    public $phone = '';

    public $settings = [];
    public $codEnabled = false;
    public $supportedCountries = [];
    public $authSettings = [
        'email_otp_enabled' => false,
        'email_password_enabled' => true,
        'guest_checkout_enabled' => false,
    ];
    public $authMethod = 'password';
    public $authPanel = 'login';
    public $showAuthSection = false;
    public $loginEmail = '';
    public $loginPassword = '';
    public $loginRemember = false;
    public $loginOtp = '';
    public $loginOtpSent = false;
    public $registerName = '';
    public $registerEmail = '';
    public $registerPassword = '';

    public $shipToDifferentAddress = false;
    public $billing = [
        'name' => '',
        'address_line1' => '',
        'address_line2' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country_code' => 'BD',
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

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'billing.name' => 'required|string|max:255',
            'billing.address_line1' => 'required|string|max:255',
            'billing.city' => 'required|string|max:100',
            'billing.country_code' => 'required|string|exists:countries,code',
            'selectedShippingMethod' => 'required|exists:shipping_methods,id',
        ];
    }

    public function boot(CartService $cartService, CustomerAuthService $customerAuthService): void
    {
        $this->cartService = $cartService;
        $this->customerAuthService = $customerAuthService;
    }

    public function mount(): void
    {
        $this->cart = $this->cartService->getCart();
        $this->shippingMethods = ShippingMethod::where('active', true)->orderBy('cost', 'asc')->get();
        $this->currencySymbol = Currency::getActive()->symbol;

        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->codEnabled = filter_var($this->settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->supportedCountries = Country::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['code', 'name'])
            ->map(fn ($country) => ['code' => $country->code, 'name' => $country->name])
            ->values()
            ->all();
        $this->authSettings = $this->customerAuthService->getAuthSettings();
        if ($this->authSettings['email_password_enabled']) {
            $this->authMethod = 'password';
        } elseif ($this->authSettings['email_otp_enabled']) {
            $this->authMethod = 'otp';
        }
        $this->authPanel = 'login';
        $this->showAuthSection = ! $this->authSettings['guest_checkout_enabled'];

        if ($this->shippingMethods->isNotEmpty()) {
            $this->selectedShippingMethod = $this->shippingMethods->first()->id;
        }

        $defaultCountry = $this->supportedCountries[0]['code'] ?? 'BD';
        if (empty($this->billing['country_code'])) {
            $this->billing['country_code'] = $defaultCountry;
        }
        if (empty($this->shipping['country_code'])) {
            $this->shipping['country_code'] = $defaultCountry;
        }

        if (Auth::check()) {
            $this->fillFromAuthenticatedUser();
        }

        $this->calculateTotals();
    }

    public function increment($key): void
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['quantity']++;
            $this->updateSession();
        }
    }

    public function decrement($key): void
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] > 1) {
                $this->cart[$key]['quantity']--;
            } else {
                unset($this->cart[$key]);
            }
            $this->updateSession();
        }
    }

    public function removeItem($key): void
    {
        unset($this->cart[$key]);
        $this->updateSession();
    }

    public function updateSession(): void
    {
        $this->cartService->putCart($this->cart);
        $this->calculateTotals();
        $this->dispatch('cartUpdated');
    }

    public function updatedSelectedShippingMethod(): void
    {
        $this->calculateTotals();
    }

    public function applyCoupon(): void
    {
        $this->resetErrorBag('coupon');

        if (empty($this->couponCode)) {
            return;
        }

        $coupon = Discount::where('code', $this->couponCode)
            ->where('active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->first();

        if (! $coupon) {
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

    public function removeCoupon(): void
    {
        $this->appliedCoupon = null;
        $this->calculateTotals();
    }

    public function calculateTotals(): void
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

    public function placeOrder(): void
    {
        if (! Auth::check() && ! $this->authSettings['guest_checkout_enabled']) {
            $this->addError('auth', 'Login is required to place an order.');
            return;
        }

        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $currency = Currency::getActive();
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'discount' => $this->discountAmount,
                'shipping_cost' => $this->shippingCost,
                'total' => $this->total,
                'currency_code' => $currency?->code ?? 'BDT',
                'exchange_rate' => $currency?->exchange_rate ?? 1.0000,
                'shipping_method_id' => $this->selectedShippingMethod,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'pending',
            ]);

            $order->addresses()->create(array_merge($this->billing, [
                'type' => 'billing',
                'email' => $this->email,
                'phone' => $this->phone,
            ]));

            $shipData = $this->shipToDifferentAddress ? $this->shipping : $this->billing;
            $order->addresses()->create(array_merge($shipData, [
                'type' => 'shipping',
                'email' => $this->email,
                'phone' => $this->phone,
            ]));

            foreach ($this->cart as $key => $item) {
                $parts = explode('-', $key);
                $productId = $parts[0];
                $rawVariationId = $parts[1] ?? null;

                $product = Product::find($productId);
                $variationId = null;
                if ($product && $product->has_variations && $rawVariationId && ctype_digit((string) $rawVariationId)) {
                    $variationId = (int) $rawVariationId;
                }

                $order->items()->create([
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'attributes' => $item['attributes'] ?? [],
                ]);

                if ($product) {
                    if ($variationId && $product->has_variations) {
                        $var = $product->variations()->find($variationId);
                        if ($var) {
                            $var->decrement('stock', $item['quantity']);
                        }
                    } else {
                        $product->decrement('stock', $item['quantity']);
                    }
                }
            }

            $this->cartService->clearCart();
            $this->cart = [];
            $this->dispatch('cartUpdated');

            $this->notifyAdminNewOrder($order);
            session()->flash('order_success', "Order #{$order->order_number} placed successfully!");
        });
    }

    protected function notifyAdminNewOrder(Order $order): void
    {
        $settings = Setting::whereIn('key', [
            'admin_notify_email_enabled',
            'admin_notify_email_address',
            'admin_notify_telegram_enabled',
            'admin_telegram_bot_token',
            'admin_telegram_chat_id',
            'mail_from_address',
            'mail_from_name',
        ])->pluck('value', 'key');

        $orderTotal = number_format((float) $order->total, 2);
        $currencyCode = $order->currency_code ?? 'BDT';
        $messageText = "New order received\\n".
            "Order: {$order->order_number}\\n".
            "Total: {$currencyCode} {$orderTotal}\\n".
            "Payment: ".($order->payment_method ?? 'N/A');

        if (filter_var($settings['admin_notify_email_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $toEmail = $settings['admin_notify_email_address'] ?? null;
            if ($toEmail) {
                $fromAddress = $settings['mail_from_address'] ?? config('mail.from.address');
                $fromName = $settings['mail_from_name'] ?? config('mail.from.name');
                try {
                    Mail::raw($messageText, function ($message) use ($toEmail, $fromAddress, $fromName) {
                        if ($fromAddress) {
                            $message->from($fromAddress, $fromName ?: null);
                        }
                        $message->to($toEmail);
                        $message->subject('New order received');
                    });
                } catch (\Throwable $e) {
                    // Silent failure to avoid blocking checkout
                }
            }
        }

        if (filter_var($settings['admin_notify_telegram_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $botToken = $settings['admin_telegram_bot_token'] ?? null;
            $chatId = $settings['admin_telegram_chat_id'] ?? null;
            if ($botToken && $chatId) {
                try {
                    Http::asForm()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $messageText,
                    ]);
                } catch (\Throwable $e) {
                    // Silent failure to avoid blocking checkout
                }
            }
        }
    }

    public function setAuthMethod(string $method): void
    {
        if ($method === 'password' && $this->authSettings['email_password_enabled']) {
            $this->authMethod = 'password';
        }

        if ($method === 'otp' && $this->authSettings['email_otp_enabled']) {
            $this->authMethod = 'otp';
        }

        $this->loginPassword = '';
        $this->loginOtp = '';
        $this->loginOtpSent = false;
        $this->resetErrorBag(['loginPassword', 'loginOtp', 'loginEmail']);
    }

    public function setAuthPanel(string $panel): void
    {
        if (! in_array($panel, ['login', 'register'], true)) {
            return;
        }

        $this->authPanel = $panel;
        $this->resetErrorBag([
            'loginEmail',
            'loginPassword',
            'loginOtp',
            'registerName',
            'registerEmail',
            'registerPassword',
        ]);
    }

    public function toggleAuthSection(): void
    {
        $this->showAuthSection = ! $this->showAuthSection;
    }

    public function sendLoginOtp(): void
    {
        if (! $this->authSettings['email_otp_enabled']) {
            $this->addError('loginOtp', 'OTP login is disabled.');
            return;
        }

        $validated = $this->validate([
            'loginEmail' => 'required|email',
        ]);

        $this->customerAuthService->sendLoginOtp($validated['loginEmail']);
        $this->loginOtpSent = true;
        session()->flash('otp_message', 'We sent a 6-digit code to your email.');
    }

    public function verifyLoginOtp(): void
    {
        $validated = $this->validate([
            'loginEmail' => 'required|email',
            'loginOtp' => 'required|numeric|digits:6',
        ]);

        $user = $this->customerAuthService->loginWithOtp($validated['loginEmail'], $validated['loginOtp']);
        if (! $user) {
            $this->addError('loginOtp', 'Invalid or expired code.');
            return;
        }

        $this->loginOtp = '';
        $this->loginOtpSent = false;
        $this->fillFromAuthenticatedUser();
        session()->flash('auth_success', 'Logged in successfully.');
    }

    public function loginWithPasswordInline(): void
    {
        if (! $this->authSettings['email_password_enabled']) {
            $this->addError('loginPassword', 'Password login is disabled.');
            return;
        }

        $validated = $this->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required|string|min:6',
        ]);

        if (! $this->customerAuthService->loginWithPassword($validated['loginEmail'], $validated['loginPassword'], (bool) $this->loginRemember)) {
            $this->addError('loginPassword', 'Invalid email or password.');
            return;
        }

        $this->loginPassword = '';
        $this->fillFromAuthenticatedUser();
        session()->flash('auth_success', 'Logged in successfully.');
    }

    public function registerInline(): void
    {
        if (! $this->authSettings['email_password_enabled']) {
            $this->addError('registerEmail', 'Registration is unavailable right now.');
            return;
        }

        $validated = $this->validate([
            'registerName' => 'required|string|max:255',
            'registerEmail' => 'required|email|unique:users,email',
            'registerPassword' => 'required|string|min:6',
        ]);

        $user = $this->customerAuthService->registerWithPassword(
            name: $validated['registerName'],
            email: $validated['registerEmail'],
            password: $validated['registerPassword'],
        );

        if (! $user) {
            $this->addError('registerEmail', 'This email is already in use.');
            return;
        }

        $this->registerName = '';
        $this->registerEmail = '';
        $this->registerPassword = '';
        $this->fillFromAuthenticatedUser();
        session()->flash('auth_success', 'Account created and logged in.');
    }

    private function fillFromAuthenticatedUser(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->loginEmail = $user->email;
        $this->authPanel = 'login';
        $this->loginOtp = '';
        $this->loginOtpSent = false;
        $this->loginPassword = '';

        $this->savedAddresses = $user->addresses()->latest()->get();

        if ($this->savedAddresses->isEmpty()) {
            $lastOrder = Order::where('user_id', $user->id)->latest()->first();
            if ($lastOrder) {
                $this->savedAddresses = $lastOrder->addresses;
            }
        }

        if ($this->savedAddresses->isNotEmpty()) {
            $latest = $this->savedAddresses->first();
            $this->useSavedAddress($latest->id);
        } else {
            $this->billing['name'] = $user->name;
        }
    }

    public function useSavedAddress($addressId): void
    {
        $address = $this->savedAddresses->where('id', $addressId)->first();

        if ($address) {
            $this->selectedAddressId = $address->id;
            $this->billing = [
                'name' => $address->name,
                'country_code' => $address->country_code,
                'address_line1' => $address->address_line1,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
            ];
            $this->phone = $address->phone;
        }
    }

    public function clearAddressSelection(): void
    {
        $defaultCountry = $this->supportedCountries[0]['code'] ?? 'BD';

        $this->selectedAddressId = 'new';
        $this->billing = [
            'name' => Auth::check() ? (Auth::user()->name ?? '') : '',
            'country_code' => $defaultCountry,
            'address_line1' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
        ];
    }

    public function render()
    {
        return view('livewire.store.cart-checkout');
    }
}
