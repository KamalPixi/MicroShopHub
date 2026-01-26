<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return view('admin.products.products');
    }

    public function create() {
        return view('admin.products.create');
    }

    public function show($id) {
        return view('admin.products.show', compact('id'));
    }

    public function edit($id) {
        return view('admin.products.edit', compact('id'));
    }

    public function products() {
        return view('admin.products.products');
    }
}
