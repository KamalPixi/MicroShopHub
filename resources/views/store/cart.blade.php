@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . __('store.cart'))

@section('content')
    @livewire('store.cart-checkout')
@endsection
