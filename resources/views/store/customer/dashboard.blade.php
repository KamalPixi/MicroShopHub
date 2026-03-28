@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Customer Dashboard')

@section('content')
    @livewire('store.customer.dashboard')
@endsection
