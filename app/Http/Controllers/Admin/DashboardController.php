<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function settings() {
        return view('admin.settings');
    }

    public function shippingMethods() {
        return view('admin.shipping-methods');
    }
}
