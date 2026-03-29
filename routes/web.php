<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Store Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\Store\CustomerController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\NewsletterController;
use App\Http\Controllers\Store\LiveChatTelegramController;
use App\Http\Controllers\Store\TelegramWebhookController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;


/*
|--------------------------------------------------------------------------
| Store (Frontend) Routes
|--------------------------------------------------------------------------
*/

Route::middleware('store.analytics')->name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::get('/search', [StoreController::class, 'search'])->name('search');
    Route::get('/product/{slug}', [StoreController::class, 'show'])->name('product.show');
    Route::get('/about', [StoreController::class, 'about'])->name('about');
    Route::get('/faq', [StoreController::class, 'faq'])->name('faq');
    Route::get('/privacy-policy', [StoreController::class, 'privacyPolicy'])->name('privacy-policy');
    Route::get('/terms', [StoreController::class, 'terms'])->name('terms');
    Route::get('/refund-policy', [StoreController::class, 'refundPolicy'])->name('refund-policy');
    Route::get('/shipping', [StoreController::class, 'shippingInfo'])->name('shipping');
    Route::get('/cookie-policy', [StoreController::class, 'cookiePolicy'])->name('cookie-policy');
    Route::get('/contact', [StoreController::class, 'contact'])->name('contact');
    Route::get('/cart', [StoreController::class, 'cart'])->name('cart.index');
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
});

Route::get('/sitemap.xml', [StoreController::class, 'sitemap'])->name('sitemap');


/*
|--------------------------------------------------------------------------
| Customer Auth & Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware('store.analytics')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
        Route::get('/email/verify', function () {
            return view('store.email-verification');
        })->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect()->route('customer.dashboard')->with('message', 'Your email address has been verified.');
        })->middleware(['signed'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            if ($request->user()->hasVerifiedEmail()) {
                return back()->with('message', 'Your email is already verified.');
            }

            $cacheKey = 'customer-email-verification-sent:'.$request->user()->id;
            $lastSentAt = Cache::get($cacheKey);

            if ($lastSentAt && now()->diffInSeconds($lastSentAt) < 120) {
                $wait = 120 - now()->diffInSeconds($lastSentAt);

                return back()->with('message', 'Please wait '.$wait.' seconds before requesting another verification link.');
            }

            $request->user()->sendEmailVerificationNotification();
            Cache::put($cacheKey, now(), now()->addMinutes(10));

            return back()->with('message', 'Verification link sent to your email. If you do not receive it, you can request a new one after 2 minutes.');
        })->middleware('throttle:6,1')->name('verification.send');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});


/*
|--------------------------------------------------------------------------
| Payment (SSLCommerz)
|--------------------------------------------------------------------------
*/

Route::prefix('payment')->name('payment.')->group(function () {    
    // Initiate Payment (Frontend posts here with 'gateway' name)
    Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');
    Route::any('/bkash/callback', [PaymentController::class, 'bkashCallback'])->name('bkash.callback');

    // Global Callbacks (Dynamic {gateway} param)
    // URL Example: /payment/sslcommerz/success
    Route::group(['prefix' => '{gateway}'], function () {
        Route::any('/success', [PaymentController::class, 'success'])->name('success');
        Route::any('/fail', [PaymentController::class, 'fail'])->name('fail');
        Route::any('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::any('/ipn', [PaymentController::class, 'ipn'])->name('ipn');
    });
});

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');
Route::post('/live-chat/telegram', [LiveChatTelegramController::class, 'handle'])->name('live-chat.telegram');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |-----------------------
        | Guest Admin Routes
        |-----------------------
        */

        Route::middleware('guest:admin')->group(function () {
            Route::get('/', [AdminAuthController::class, 'login'])->name('login');
            Route::get('/password-request', [AdminAuthController::class, 'passwordRequest'])->name('password.request');
            Route::post('/password-email', [AdminAuthController::class, 'sendPasswordResetLink'])->name('password.email');
            Route::get('/reset-password/{token}', [AdminAuthController::class, 'resetPasswordForm'])->name('password.reset');
            Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('password.update');
        });


        /*
        |-----------------------
        | Protected Admin Routes
        |-----------------------
        */

        Route::middleware(['auth:admin'])->group(function () {

            Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

            // Dashboard
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
            Route::get('/pages', [DashboardController::class, 'pages'])->name('pages');
            Route::get('/pages/about', [DashboardController::class, 'aboutPage'])->name('pages.about');
            Route::get('/pages/faq', [DashboardController::class, 'faqPage'])->name('pages.faq');
            Route::get('/pages/privacy-policy', [DashboardController::class, 'privacyPolicyPage'])->name('pages.privacy');
            Route::get('/pages/terms', [DashboardController::class, 'termsPage'])->name('pages.terms');
            Route::get('/pages/refund-policy', [DashboardController::class, 'refundPolicyPage'])->name('pages.refund');
            Route::get('/pages/shipping-info', [DashboardController::class, 'shippingInfoPage'])->name('pages.shipping');
            Route::get('/pages/cookie-policy', [DashboardController::class, 'cookiePolicyPage'])->name('pages.cookie');
            Route::get('/contact-messages', [DashboardController::class, 'contactMessages'])->name('contact.messages');

            Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
            Route::get('/shipping-methods', [DashboardController::class, 'shippingMethods'])->name('shipping.methods');
            Route::get('/homepage-settings', [DashboardController::class, 'homepageSettings'])->name('homepage.settings');
            Route::get('/marketing/subscriptions', [DashboardController::class, 'marketingSubscriptions'])->name('marketing.subscriptions');
            Route::get('/marketing/campaigns', [DashboardController::class, 'marketingCampaigns'])->name('marketing.campaigns');
            Route::get('/marketing/flash-sales', [DashboardController::class, 'marketingFlashSales'])->name('marketing.flash-sales');

            // Resources
            Route::resource('products', ProductController::class)->except(['destroy']);
            Route::resource('discounts', DiscountController::class)->only(['index']);
            Route::resource('customers', AdminCustomerController::class)->except(['create', 'store']);
            Route::resource('orders', OrderController::class)->only(['index', 'show']);
            Route::resource('users', UserController::class)->only(['index']);
        });
    });
