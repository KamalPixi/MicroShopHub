@extends('store.layouts.app')

@section('title', ($siteStoreName ?? config('app.name', 'ShopHub')) . ' - Register')

@section('content')
    @livewire('store.auth.register')
@endsection
