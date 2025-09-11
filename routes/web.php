<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
->name('admin.')
->namespace('App\Http\Controllers\Admin')
->group(function () {

    // ------------------
    // Open routes
    // ------------------
    Route::controller('AuthController')->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::get('/password-request', 'passwordRequest')->name('password.request');
    });

    // ------------------
    // Protected routes
    // ------------------
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::controller('DashboardController')->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
        });

        // Products
        Route::controller('ProductController')->group(function () {
            Route::get('/products', 'index')->name('products.index');
            Route::get('/products/create', 'create')->name('products.create');
            Route::get('/products/show', 'show')->name('products.show');
            Route::get('/products/edit', 'edit')->name('products.edit');
        });

        // Categories
        Route::controller('CategoryController')->group(function () {
            Route::get('/categories', 'index')->name('categories.index');
        });

        // Attributes
        Route::controller('AttributeController')->group(function () {
            Route::get('/attributes', 'index')->name('attributes.index');
        });

        // Discounts
        Route::controller('DiscountController')->group(function () {
            Route::get('/discounts', 'index')->name('discounts.index');
        });

    }); // end protected routes

});
