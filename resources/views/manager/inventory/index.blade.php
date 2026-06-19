@extends('layouts.manager', ['active' => 'inventory'])

@section('title', 'Inventory Overview - PadSync')
@section('page_title', 'Inventory Overview')
@section('page_subtitle', 'Monitor stock levels, projected demand, and warehouse health.')

@section('content')
    <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900">
        {{ __('Warehouse logs and threshold alerts will be added here.') }}
    </div>
@endsection
