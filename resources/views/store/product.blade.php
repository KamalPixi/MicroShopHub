@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . $product->name)

@section('content')
    @livewire('store.product-details', ['product' => $product, 'relatedProducts' => $relatedProducts])
@endsection
