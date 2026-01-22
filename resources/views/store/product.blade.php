@extends('layouts.app')

@section('content')
    @livewire('product-details', ['product' => $product, 'relatedProducts' => $relatedProducts])
@endsection
