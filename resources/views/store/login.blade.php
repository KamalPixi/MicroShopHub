@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . __('store.login'))

@section('content')
    @livewire('store.auth.login')
@endsection
