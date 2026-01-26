<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\Store\CustomerController;
use App\Http\Controllers\Store\AuthController;
use App\Http\Controllers\Store\SslCommerzController;

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

Route::name('store.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::get('/search', [StoreController::class, 'search'])->name('search');
    Route::get('/product/{slug}', [StoreController::class, 'show'])->name('product.show');
    Route::get('/cart', [StoreController::class, 'cart'])->name('cart.index');
});


/*
|--------------------------------------------------------------------------
| Customer Auth & Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


/*
|--------------------------------------------------------------------------
| Payment (SSLCommerz)
|--------------------------------------------------------------------------
*/

Route::prefix('sslcommerz')
    ->name('ssl.')
    ->group(function () {
        Route::post('/pay', [SslCommerzController::class, 'index'])->name('pay');
        Route::post('/success', [SslCommerzController::class, 'success'])->name('success');
        Route::post('/fail', [SslCommerzController::class, 'fail'])->name('fail');
        Route::post('/cancel', [SslCommerzController::class, 'cancel'])->name('cancel');
        Route::post('/ipn', [SslCommerzController::class, 'ipn'])->name('ipn');
    });


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

            Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
            Route::get('/shipping-methods', [DashboardController::class, 'shippingMethods'])->name('shipping.methods');
            Route::get('/homepage-settings', [DashboardController::class, 'homepageSettings'])->name('homepage.settings');

            // Resources
            Route::resource('products', ProductController::class)->except(['destroy']);
            Route::resource('discounts', DiscountController::class)->only(['index']);
            Route::resource('customers', AdminCustomerController::class)->except(['create', 'store']);
            Route::resource('orders', OrderController::class)->only(['index', 'show']);
            Route::resource('users', UserController::class)->only(['index']);
        });
    });

