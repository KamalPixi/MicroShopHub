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

    public function pages() {
        return view('admin.pages');
    }

    public function categories() {
        return view('admin.categories');
    }

    public function shippingMethods() {
        return view('admin.shipping-methods');
    }

    public function homepageSettings() {
        return view('admin.homepage-settings');
    }

    public function marketingSubscriptions() {
        return view('admin.marketing-subscriptions');
    }

    public function marketingCampaigns() {
        return view('admin.marketing-campaigns');
    }
}
