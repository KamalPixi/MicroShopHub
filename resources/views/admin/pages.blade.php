@extends('admin.layouts.default')
@section('content')
    @include('admin.includes.breadcrumb')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.pages.privacy') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-primary">
            <p class="text-sm font-semibold text-gray-800">Privacy Policy</p>
            <p class="mt-1 text-xs text-gray-500">Dedicated editor for privacy content.</p>
        </a>
        <a href="{{ route('admin.pages.terms') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-primary">
            <p class="text-sm font-semibold text-gray-800">Terms of Service</p>
            <p class="mt-1 text-xs text-gray-500">Dedicated editor for terms content.</p>
        </a>
        <a href="{{ route('admin.pages.cookie') }}" class="rounded-xl border border-gray-200 bg-white p-5 hover:border-primary">
            <p class="text-sm font-semibold text-gray-800">Cookie Policy</p>
            <p class="mt-1 text-xs text-gray-500">Dedicated editor for cookie policy content.</p>
        </a>
    </div>
@endsection
