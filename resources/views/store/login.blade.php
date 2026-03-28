@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Login')

@section('content')
    @livewire('store.auth.login')
@endsection
