@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Verify Email')

@section('content')
    <div class="bg-gray-50 min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl rounded-3xl border border-gray-200 bg-white p-8 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 4H8m8-8H8m12 6.364A2 2 0 0119.414 16l-7.07 7.07a2 2 0 01-2.829 0L2.455 9.09a2 2 0 010-2.828l7.07-7.071A2 2 0 0111 4.586h6A2.414 2.414 0 0119.414 7v5.364z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">Email verification</p>
                    <h1 class="text-2xl font-bold text-gray-900">Check your inbox</h1>
                </div>
            </div>

            <p class="mt-4 text-sm leading-6 text-gray-600">
                Your customer account needs one quick email check. Send the verification link to {{ auth()->user()->email }} and open it from your inbox to confirm your address.
            </p>

            @if (session('message'))
                <div class="mt-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white transition hover:bg-primary/90">
                        Send verification link
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700 transition hover:border-primary/30 hover:text-primary hover:bg-gray-50">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
