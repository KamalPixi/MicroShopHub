@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Cart')

@section('content')
    @livewire('store.cart-checkout')
@endsection
