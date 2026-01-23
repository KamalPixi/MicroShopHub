<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CustomerController;

Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::get('/search', [StoreController::class, 'search'])->name('store.search');
Route::get('/product/{slug}', [StoreController::class, 'show'])->name('store.product');
Route::get('/cart', [StoreController::class, 'cart'])->name('cart.index');


Route::controller(CustomerController::class)->middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'dashboard')->name('customer.dashboard');
    Route::get('/logout', 'logout')->name('logout');
    
});

Route::prefix('admin')
->name('admin.')
->namespace('App\Http\Controllers\Admin')
->group(function () {

    // ------------------
    // Open routes
    // ------------------
    Route::controller('AuthController')->group(function () {
        Route::get('/', 'login')->name('login');
        Route::get('/password-request', 'passwordRequest')->name('password.request');
    });

    // ------------------
    // Protected routes
    // ------------------
    Route::middleware(['auth.admin'])->group(function () {

        Route::controller('AuthController')->group(function () {
            Route::get('/logout', 'logout')->name('logout');
        });

        // Dashboard
        Route::controller('DashboardController')->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
            Route::get('/settings', 'settings')->name('settings');
            Route::get('/shipping-methods', 'shippingMethods')->name('shipping-methods');
            Route::get('/categories', 'categories')->name('categories');
            Route::get('/homepage-settings', 'homepageSettings')->name('homepage-settings');
        });

        // Products
        Route::controller('ProductController')->group(function () {
            Route::get('/products', 'index')->name('products.index');
            Route::get('/products/create', 'create')->name('products.create');
            Route::get('/products/show', 'show')->name('products.show');
            Route::get('/products/edit', 'edit')->name('products.edit');
        });

        // Discounts
        Route::controller('DiscountController')->group(function () {
            Route::get('/discounts', 'index')->name('discounts.index');
        });

        Route::controller('CustomerController')->group(function () {
            Route::get('/customers', 'index')->name('customers');
            Route::get('/customers/show', 'show')->name('customers.show');
            Route::get('/customers/edit', 'edit')->name('customers.edit');
        });

        Route::controller('OrderController')->group(function () {
            Route::get('/orders', 'index')->name('orders');
        });

        Route::controller('UserController')->group(function () {
            Route::get('/users', 'index')->name('users');
        });

    }); // end protected routes

});
