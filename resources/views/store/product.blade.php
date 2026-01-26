@extends('store.layouts.app')

@section('content')
    @livewire('store.product-details', ['product' => $product, 'relatedProducts' => $relatedProducts])
@endsection
