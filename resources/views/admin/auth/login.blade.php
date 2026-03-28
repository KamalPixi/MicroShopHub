@extends('admin.layouts.auth')

@section('content')
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Admin Login</h1>
        </div>

        @livewire('Admin.Login')

        <div class="mt-4 text-center">
            <a href="{{ route('admin.password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot your password?</a>
        </div>
    </div>
@endsection
