@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . __('store.dashboard'))

@section('content')
    @livewire('store.customer.dashboard')
@endsection
