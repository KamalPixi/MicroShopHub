<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customers.customers');
    }

    public function show(User $customer)
    {
        $customer->load(['addresses', 'orders' => function ($query) {
            $query->latest()->limit(8);
        }]);

        return view('admin.customers.show', [
            'customer' => $customer,
        ]);
    }

    public function edit(User $customer)
    {
        return view('admin.customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, User $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$customer->id,
            'phone' => 'nullable|string|max:50',
            'gender' => 'nullable|integer|in:1,2,3',
            'birthday' => 'nullable|date',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer->id)
            ->with('message', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('message', 'Customer deleted successfully.');
    }
}
