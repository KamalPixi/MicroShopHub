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
use App\Jobs\SendAdminOrderNotification;
use Livewire\Component;
use Livewire\WithFileUploads;

class CartCheckout extends Component
{
    use WithFileUploads;
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
    public bool $deliveryConfirmed = false;
    public array $servicePoints = [];
    public ?int $selectedServicePointId = null;
    public string $selectedServicePointName = '';
    public string $servicePointError = '';
    public $couponCode = '';
    public $appliedCoupon = null;
    public $paymentMethod = '';
    public $offlinePaymentMethods = [];
    public $offlinePaymentMethodId = '';
    public $offlineReference = '';
    public $offlineProof;

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

    protected $validationAttributes = [
        'offlinePaymentMethodId' => 'offline payment method',
    ];

    protected function rules()
    {
        if ($this->isShopPickupSelected()) {
            return [
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'billing.name' => 'required|string|max:255',
                'billing.address_line1' => 'nullable|string|max:255',
                'billing.city' => 'nullable|string|max:100',
                'billing.country_code' => 'nullable|string|max:2',
                'selectedShippingMethod' => 'required|exists:shipping_methods,id',
            ];
        }

        if ($this->isServicePointMethodSelected()) {
            return [
                'email' => 'required|email',
                'phone' => 'nullable|string|max:20',
                'billing.name' => 'required|string|max:255',
                'billing.country_code' => 'required|string|exists:countries,code',
                'billing.postal_code' => 'required|string|max:20',
                'billing.address_line1' => 'nullable|string|max:255',
                'billing.city' => 'nullable|string|max:100',
                'selectedShippingMethod' => 'required|exists:shipping_methods,id',
            ];
        }

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
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $sendcloudEnabled = filter_var($this->settings['sendcloud_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $shopPickupEnabled = filter_var($this->settings['shop_pickup_enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $shippingQuery = ShippingMethod::with('categories')->where('active', true);
        if (! $sendcloudEnabled) {
            $shippingQuery->where(function ($q) {
                $q->whereNull('provider')->orWhere('provider', '!=', 'sendcloud');
            });
        }
        if (! $shopPickupEnabled) {
            $shippingQuery->where('type', '!=', 'local_pickup');
        }
        $allActiveMethods = $shippingQuery->orderBy('cost', 'asc')->get();

        $productIds = collect($this->cart)->pluck('product_id')->filter()->unique()->values()->all();
        if (! empty($productIds)) {
            $cartCategoryIds = Product::whereIn('id', $productIds)
                ->with('categories')
                ->get()
                ->flatMap(fn ($product) => $product->categories->flatMap(fn ($cat) => array_filter([$cat->id, $cat->parent_id])))
                ->unique()
                ->values();

            if ($cartCategoryIds->isNotEmpty()) {
                $filteredMethods = $allActiveMethods->filter(function ($method) use ($cartCategoryIds) {
                    if ($method->categories->isEmpty()) {
                        return true;
                    }

                    return $method->categories->pluck('id')->intersect($cartCategoryIds)->isNotEmpty();
                })->values();

                $this->shippingMethods = $filteredMethods->isNotEmpty() ? $filteredMethods : $allActiveMethods;
            } else {
                $this->shippingMethods = $allActiveMethods;
            }
        } else {
            $this->shippingMethods = $allActiveMethods;
        }
        $this->currencySymbol = Currency::getActive()->symbol;
        $this->codEnabled = filter_var($this->settings['cod_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $rawOffline = $this->settings['offline_payment_methods'] ?? '[]';
        $decodedOffline = is_string($rawOffline) ? json_decode($rawOffline, true) : $rawOffline;
        $this->offlinePaymentMethods = collect(is_array($decodedOffline) ? $decodedOffline : [])
            ->filter(fn ($method) => ! empty($method['active']))
            ->values()
            ->all();
        if (! empty($this->offlinePaymentMethods)) {
            $this->offlinePaymentMethodId = '';
        }
        $priorityOrder = ['FR', 'BE', 'CH', 'DE', 'ES', 'IT', 'LU'];
        $this->supportedCountries = Country::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['code', 'name'])
            ->sortBy(function ($country) use ($priorityOrder) {
                $index = array_search($country->code, $priorityOrder, true);
                return $index === false ? [1, $country->name] : [0, $index];
            })
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

        $this->syncSelectedPaymentMethod();

        $defaultCountry = $this->getDefaultCountryCode();
        if (empty($this->billing['country_code'])) {
            $this->billing['country_code'] = $defaultCountry;
        }
        if (empty($this->shipping['country_code'])) {
            $this->shipping['country_code'] = $defaultCountry;
        }

        if (Auth::check()) {
            $this->fillFromAuthenticatedUser();
            $this->restoreUserDraft();
        } else {
            $this->restoreGuestDraft();
        }

        $this->restoreCouponDraft();

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
        $this->deliveryConfirmed = false;
        $this->servicePoints = [];
        $this->selectedServicePointId = null;
        $this->selectedServicePointName = '';
        $this->servicePointError = '';
        $this->syncSelectedPaymentMethod();
        $this->calculateTotals();
    }

    public function isShopPickupSelected(): bool
    {
        $method = collect($this->shippingMethods)->firstWhere('id', $this->selectedShippingMethod);

        return $method && ($method->type ?? 'flat_rate') === 'local_pickup';
    }

    public function isStripeConfigured(): bool
    {
        $mode = ($this->settings['stripe_mode'] ?? 'test') === 'production' ? 'production' : 'test';
        $key = $mode === 'test'
            ? trim((string) ($this->settings['stripe_test_api_key'] ?? ''))
            : trim((string) ($this->settings['stripe_api_key'] ?? ''));

        return $key !== '';
    }

    public function availablePaymentGateways(): array
    {
        $gateways = [];

        if ($this->isShopPickupSelected()) {
            $gateways[] = 'pay_at_shop';
        }
        if ($this->codEnabled) {
            $gateways[] = 'cod';
        }
        if (! empty($this->settings['sslcommerz_store_id'])) {
            $gateways[] = 'sslcommerz';
        }
        if ($this->isStripeConfigured()) {
            $gateways[] = 'stripe';
        }
        if (! empty($this->settings['bkash_app_key'])) {
            $gateways[] = 'bkash';
        }
        if (! empty($this->settings['portpos_app_key'])) {
            $gateways[] = 'portpos';
        }
        if (! empty($this->offlinePaymentMethods)) {
            $gateways[] = 'offline';
        }

        return $gateways;
    }

    protected function syncSelectedPaymentMethod(): void
    {
        $available = $this->availablePaymentGateways();

        if (! in_array($this->paymentMethod, $available, true)) {
            $this->paymentMethod = $available[0] ?? '';
        }
    }

    public function confirmDeliveryStep(): void
    {
        $this->servicePointError = '';

        if (! $this->selectedShippingMethod) {
            $this->addError('selectedShippingMethod', 'Please choose a delivery method.');
            return;
        }

        if ($this->isServicePointMethodSelected() && ! $this->selectedServicePointId) {
            $this->servicePointError = 'Please find and select a nearby pickup point before continuing.';
            return;
        }

        $this->deliveryConfirmed = true;
    }

    public function editDeliveryStep(): void
    {
        $this->deliveryConfirmed = false;
    }

    protected function resolveLocationCountry(array $address): string
    {
        $country = strtoupper(trim((string) ($address['country_code'] ?? '')));
        if ($country !== '') {
            return $country;
        }

        $billingCountry = strtoupper(trim((string) ($this->billing['country_code'] ?? '')));
        if ($billingCountry !== '') {
            return $billingCountry;
        }

        return strtoupper((string) ($this->supportedCountries[0]['code'] ?? 'FR'));
    }

    public function getBillingStatesProperty(): array
    {
        $locations = config('europe_locations', []);
        $country = $this->resolveLocationCountry($this->billing);

        return isset($locations[$country]) ? array_keys($locations[$country]) : [];
    }

    public function getBillingCitiesProperty(): array
    {
        $locations = config('europe_locations', []);
        $country = $this->resolveLocationCountry($this->billing);
        $state = trim((string) ($this->billing['state'] ?? ''));

        if (! isset($locations[$country])) {
            return [];
        }

        if ($state !== '' && isset($locations[$country][$state])) {
            return $locations[$country][$state];
        }

        return collect($locations[$country])->flatten()->unique()->values()->all();
    }

    public function getShippingStatesProperty(): array
    {
        $locations = config('europe_locations', []);
        $country = $this->resolveLocationCountry($this->shipping);

        return isset($locations[$country]) ? array_keys($locations[$country]) : [];
    }

    public function getShippingCitiesProperty(): array
    {
        $locations = config('europe_locations', []);
        $country = $this->resolveLocationCountry($this->shipping);
        $state = trim((string) ($this->shipping['state'] ?? ''));

        if (! isset($locations[$country])) {
            return [];
        }

        if ($state !== '' && isset($locations[$country][$state])) {
            return $locations[$country][$state];
        }

        return collect($locations[$country])->flatten()->unique()->values()->all();
    }

    public function isServicePointMethodSelected(): bool
    {
        $method = collect($this->shippingMethods)->firstWhere('id', $this->selectedShippingMethod);

        return $method && $method->delivery_mode === 'service_point';
    }

    public function findServicePoints(): void
    {
        $this->servicePointError = '';
        $this->servicePoints = [];

        $source = $this->shipToDifferentAddress ? $this->shipping : $this->billing;
        $country = strtoupper(trim((string) ($source['country_code'] ?? '')));
        $postal = trim((string) ($source['postal_code'] ?? ''));

        if ($country === '' || $postal === '') {
            $this->servicePointError = 'Please enter your Country and Zip / Postal Code first to find nearby pickup points.';
            return;
        }

        try {
            $method = collect($this->shippingMethods)->firstWhere('id', $this->selectedShippingMethod);
            $name = strtolower((string) ($method?->name ?? ''));
            $carrier = null;
            if (str_contains($name, 'chrono')) {
                $carrier = 'chronopost';
            } elseif (str_contains($name, 'mondial')) {
                $carrier = 'mondial_relay';
            }

            $this->servicePoints = app(\App\Services\SendcloudService::class)->getServicePoints($country, $postal, $carrier);
            if (empty($this->servicePoints)) {
                $this->servicePointError = 'No pickup points found for that postal code. Try another postal code.';
            }
        } catch (\Throwable $e) {
            $this->servicePointError = $e->getMessage();
        }
    }

    public function selectServicePoint(int $id, string $name): void
    {
        $this->selectedServicePointId = $id;
        $this->selectedServicePointName = $name;
    }

    public function updatedEmail(): void
    {
        $this->saveGuestDraft();
    }

    public function updatedPhone(): void
    {
        $this->saveGuestDraft();
    }

    public function updatedBilling(): void
    {
        $this->saveGuestDraft();
    }

    public function updatedShipping(): void
    {
        $this->saveGuestDraft();
    }

    public function updatedShipToDifferentAddress(): void
    {
        $this->saveGuestDraft();
    }

    public function applyCoupon(): void
    {
        $this->resetErrorBag('coupon');

        $code = strtoupper(trim((string) $this->couponCode));

        if ($code === '') {
            return;
        }

        $coupon = $this->findValidCoupon($code);

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
        $this->saveGuestDraft();
        session()->flash('coupon_success', 'Coupon applied successfully!');
    }

    public function removeCoupon(): void
    {
        $this->appliedCoupon = null;
        $this->couponCode = '';
        $this->calculateTotals();
        $this->saveGuestDraft();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        if ($this->appliedCoupon && ! $this->isCouponEligible($this->appliedCoupon)) {
            $this->appliedCoupon = null;
            $this->saveCouponDraft();
        }

        $this->discountAmount = 0;
        if ($this->appliedCoupon) {
            if ($this->appliedCoupon->type === 'percentage') {
                $this->discountAmount = ($this->subtotal * $this->appliedCoupon->value) / 100;
            } elseif ($this->appliedCoupon->type === 'fixed') {
                $this->discountAmount = $this->appliedCoupon->value;
            }
        }

        $method = $this->shippingMethods->find($this->selectedShippingMethod);
        $shippingCost = $method ? $method->cost : 0;
        if ($this->appliedCoupon?->type === 'free_shipping') {
            $shippingCost = 0;
        }

        $this->discountAmount = min($this->subtotal, (float) $this->discountAmount);
        $this->shippingCost = $shippingCost;
        $this->total = max(0, ($this->subtotal - $this->discountAmount) + $this->shippingCost);
    }

    public function placeOrder(): void
    {
        $order = $this->createCheckoutOrder(true);
        if (! $order) {
            return;
        }

        $this->notifyAdminNewOrder($order);
        session()->flash('order_success', "Order #{$order->order_number} placed successfully!");
    }

    public function proceedToGatewayPayment(): void
    {
        $order = $this->createCheckoutOrder(false);
        if (! $order) {
            return;
        }

        session()->put('pending_payment_order_number', $order->order_number);
        $this->dispatch('submit-gateway-payment', orderNumber: $order->order_number);
    }

    protected function createCheckoutOrder(bool $clearCart = true): ?Order
    {
        if (! Auth::check() && ! $this->authSettings['guest_checkout_enabled']) {
            $this->addError('auth', 'Login is required to place an order.');
            return null;
        }

        if (empty($this->cart)) {
            $this->addError('cart', 'Your cart is empty.');
            return null;
        }

        if ($this->isServicePointMethodSelected() && ! $this->selectedServicePointId) {
            $this->servicePointError = 'Please find and select a nearby pickup point before continuing.';
            $this->addError('selectedShippingMethod', 'Please select a nearby pickup point.');
            return null;
        }

        $this->validate();

        $availableGateways = $this->availablePaymentGateways();
        if ($this->paymentMethod === '' || ! in_array($this->paymentMethod, $availableGateways, true)) {
            $this->addError('paymentMethod', 'Please select a valid payment method before placing your order.');
            return null;
        }

        if ($this->paymentMethod === 'offline') {
            $this->validate([
                'offlinePaymentMethodId' => 'required',
                'offlineReference' => 'nullable|string|max:100',
                'offlineProof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            ]);
            if (trim((string) $this->offlineReference) === '' && ! $this->offlineProof) {
                $this->addError('offlineReference', 'Provide a reference or upload a proof.');
                return null;
            }
        }

        if ($this->appliedCoupon && ! $this->isCouponEligible($this->appliedCoupon)) {
            $this->addError('coupon', 'Coupon is no longer valid for this cart.');
            return null;
        }

        $createdOrder = null;

        DB::transaction(function () use (&$createdOrder, $clearCart) {
            $currency = Currency::getActive();
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'discount' => $this->discountAmount,
                'shipping_cost' => $this->shippingCost,
                'total' => $this->total,
                'currency_code' => $currency?->code ?? 'EUR',
                'exchange_rate' => $currency?->exchange_rate ?? 1.0000,
                'shipping_method_id' => $this->selectedShippingMethod,
                'sendcloud_service_point_id' => $this->selectedServicePointId,
                'sendcloud_service_point_name' => $this->selectedServicePointName !== '' ? $this->selectedServicePointName : null,
                'payment_method' => $this->paymentMethod,
                'payment_status' => $this->paymentMethod === 'offline' ? 'pending_verification' : 'pending',
            ]);

            if ($this->appliedCoupon) {
                $order->discounts()->attach($this->appliedCoupon->id, [
                    'applied_value' => $this->discountAmount,
                ]);
            }

            $billingData = $this->billing;
            if ($this->isShopPickupSelected()) {
                $billingData['address_line1'] = trim((string) ($billingData['address_line1'] ?? '')) ?: 'In-Store Pickup';
                $billingData['city'] = trim((string) ($billingData['city'] ?? '')) ?: 'Shop Pickup';
                $billingData['country_code'] = trim((string) ($billingData['country_code'] ?? '')) ?: $this->getDefaultCountryCode();
            } elseif ($this->isServicePointMethodSelected()) {
                $pickedPoint = collect($this->servicePoints)->firstWhere('id', $this->selectedServicePointId);
                $billingData['address_line1'] = trim((string) ($billingData['address_line1'] ?? ''))
                    ?: ($pickedPoint['street'] ?? $this->selectedServicePointName ?: 'Pickup Point');
                $billingData['city'] = trim((string) ($billingData['city'] ?? ''))
                    ?: ($pickedPoint['city'] ?? 'Pickup Point');
                $billingData['postal_code'] = trim((string) ($billingData['postal_code'] ?? ''))
                    ?: ($pickedPoint['postal_code'] ?? '');
                $billingData['country_code'] = trim((string) ($billingData['country_code'] ?? '')) ?: $this->getDefaultCountryCode();
            }

            $order->addresses()->create(array_merge($billingData, [
                'type' => 'billing',
                'email' => $this->email,
                'phone' => $this->phone,
            ]));

            $shipData = ($this->shipToDifferentAddress && ! $this->isShopPickupSelected() && ! $this->isServicePointMethodSelected())
                ? $this->shipping
                : $billingData;
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

            if ($this->paymentMethod === 'offline') {
                $methodIndex = (int) $this->offlinePaymentMethodId;
                $selectedMethod = $this->offlinePaymentMethods[$methodIndex] ?? null;
                $attachmentPath = $this->offlineProof?->store('offline-payments', config('filesystems.default'));
                $order->offlinePayments()->create([
                    'method_name' => $selectedMethod['name'] ?? 'Offline Payment',
                    'instructions' => $selectedMethod['instructions'] ?? null,
                    'reference' => $this->offlineReference ?: null,
                    'amount' => $this->total,
                    'attachment_path' => $attachmentPath,
                    'status' => 'pending',
                ]);
            }

            if ($clearCart) {
                $this->cartService->clearCart();
                $this->cart = [];
                $this->dispatch('cartUpdated');
                session()->forget(['guest_checkout', 'auth_checkout', 'guest_coupon', 'auth_coupon']);
            }

            $createdOrder = $order;
        });

        return $createdOrder;
    }

    protected function notifyAdminNewOrder(Order $order): void
    {
        $emailEnabled = filter_var($this->settings['admin_notify_email_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $telegramEnabled = filter_var($this->settings['admin_notify_telegram_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $emailEnabled && ! $telegramEnabled) {
            return;
        }

        SendAdminOrderNotification::dispatch($order->id)->afterCommit();
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
        $this->redirect(request()->header('Referer') ?? '/checkout');
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
        $this->redirect(request()->header('Referer') ?? '/checkout');
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
        $this->redirect(request()->header('Referer') ?? '/checkout');
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

    public function saveGuestDraft(): void
    {
        $key = Auth::check() ? 'auth_checkout' : 'guest_checkout';
        session([
            $key => [
                'email' => $this->email,
                'phone' => $this->phone,
                'billing' => $this->billing,
                'shipping' => $this->shipping,
                'ship_to_different' => (bool) $this->shipToDifferentAddress,
            ],
        ]);

        $this->saveCouponDraft();
    }

    protected function restoreGuestDraft(): void
    {
        $draft = session('guest_checkout', []);
        if (! is_array($draft)) {
            return;
        }

        $this->email = (string) ($draft['email'] ?? $this->email);
        $this->phone = (string) ($draft['phone'] ?? $this->phone);
        if (isset($draft['billing']) && is_array($draft['billing'])) {
            $this->billing = array_merge($this->billing, $draft['billing']);
        }
        if (isset($draft['shipping']) && is_array($draft['shipping'])) {
            $this->shipping = array_merge($this->shipping, $draft['shipping']);
        }
        $this->shipToDifferentAddress = (bool) ($draft['ship_to_different'] ?? $this->shipToDifferentAddress);
    }

    protected function restoreUserDraft(): void
    {
        $draft = session('auth_checkout', []);
        if (! is_array($draft)) {
            return;
        }

        $this->email = (string) ($draft['email'] ?? $this->email);
        $this->phone = (string) ($draft['phone'] ?? $this->phone);
        if (isset($draft['billing']) && is_array($draft['billing'])) {
            $this->billing = array_merge($this->billing, $draft['billing']);
        }
        if (isset($draft['shipping']) && is_array($draft['shipping'])) {
            $this->shipping = array_merge($this->shipping, $draft['shipping']);
        }
        $this->shipToDifferentAddress = (bool) ($draft['ship_to_different'] ?? $this->shipToDifferentAddress);
    }

    protected function restoreCouponDraft(): void
    {
        $couponSessionKey = Auth::check() ? 'auth_coupon' : 'guest_coupon';
        $draft = session($couponSessionKey, []);

        if (! is_array($draft)) {
            return;
        }

        $coupon = null;
        $couponId = (int) ($draft['coupon_id'] ?? 0);
        $couponCode = trim((string) ($draft['coupon_code'] ?? ''));

        if ($couponId > 0) {
            $coupon = Discount::find($couponId);
        } elseif ($couponCode !== '') {
            $coupon = $this->findValidCoupon($couponCode);
        }

        if (! $coupon || ! $this->isCouponEligible($coupon)) {
            session()->forget($couponSessionKey);
            return;
        }

        $this->appliedCoupon = $coupon;
    }

    protected function saveCouponDraft(): void
    {
        $couponSessionKey = Auth::check() ? 'auth_coupon' : 'guest_coupon';

        if (! $this->appliedCoupon) {
            session()->forget($couponSessionKey);
            return;
        }

        session([
            $couponSessionKey => [
                'coupon_id' => $this->appliedCoupon->id,
                'coupon_code' => $this->appliedCoupon->code,
            ],
        ]);
    }

    protected function findValidCoupon(string $code): ?Discount
    {
        return Discount::whereRaw('UPPER(code) = ?', [$code])
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
    }

    protected function isCouponEligible(Discount $coupon): bool
    {
        if (! $coupon->active) {
            return false;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return false;
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return false;
        }

        if ($coupon->usage_limit !== null && $coupon->orders()->count() >= $coupon->usage_limit) {
            return false;
        }

        if (Auth::check() && $coupon->per_user_limit !== null) {
            $userUses = $coupon->orders()->where('user_id', Auth::id())->count();
            if ($userUses >= $coupon->per_user_limit) {
                return false;
            }
        }

        if ($this->subtotal < (float) $coupon->min_order_amount) {
            return false;
        }

        return true;
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
        $defaultCountry = $this->getDefaultCountryCode();

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

    protected function getDefaultCountryCode(): string
    {
        $codes = collect($this->supportedCountries)->pluck('code')->all();

        if (in_array('FR', $codes, true)) {
            return 'FR';
        }

        return $codes[0] ?? 'FR';
    }

    public function render()
    {
        return view('livewire.store.cart-checkout');
    }
}
