@extends('admin.layouts.default')

@section('content')
    @include('admin.includes.breadcrumb')
    @livewire('Admin.ProductEdit', ['id' => $id])
@endsection
