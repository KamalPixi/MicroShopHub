@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . $pageTitle)

@section('content')
    <div class="mx-auto max-w-4xl py-6 md:py-8">
        <div class="rounded-3xl border border-gray-200 bg-white p-5 md:p-8 shadow-sm">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $pageTitle }}</h1>
            <div class="mt-4 space-y-4 text-sm leading-7 text-gray-700">
                @foreach(preg_split("/\n{2,}/", trim((string) $pageContent)) as $paragraph)
                    <p>{!! nl2br(e($paragraph)) !!}</p>
                @endforeach
            </div>
        </div>
    </div>
@endsection
