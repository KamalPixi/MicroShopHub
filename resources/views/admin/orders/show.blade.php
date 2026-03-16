@extends('admin.layouts.default')

@section('content')
    @livewire('Admin.OrderShow', ['id' => $order->id])
@endsection
