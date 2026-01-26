@extends('admin.layouts.default')

@section('content')
    @include('admin.includes.breadcrumb')
    @livewire('Admin.ProductShow', ['id' => $id])
@endsection
