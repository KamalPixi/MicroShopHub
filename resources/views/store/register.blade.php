@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - ' . __('store.register'))

@section('content')
    @livewire('store.auth.register')
@endsection
