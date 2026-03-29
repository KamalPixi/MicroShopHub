<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function settings() {
        return view('admin.settings');
    }

    public function pages() {
        return view('admin.pages');
    }

    public function aboutPage() {
        return view('admin.pages.about');
    }

    public function faqPage() {
        return view('admin.pages.faq');
    }

    public function privacyPolicyPage() {
        return view('admin.pages.privacy-policy');
    }

    public function termsPage() {
        return view('admin.pages.terms');
    }

    public function refundPolicyPage() {
        return view('admin.pages.refund-policy');
    }

    public function shippingInfoPage() {
        return view('admin.pages.shipping-info');
    }

    public function cookiePolicyPage() {
        return view('admin.pages.cookie-policy');
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

    public function marketingFlashSales() {
        return view('admin.flash-sales');
    }

    public function contactMessages() {
        return view('admin.contact-messages');
    }
}
