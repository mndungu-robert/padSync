@extends('layouts.manager', ['active' => 'dashboard'])

@section('title', 'Program Manager Dashboard - PadSync')
@section('page_title', 'Program Manager Dashboard')
@section('page_subtitle', 'High-level operational visibility for schools, coordinators, and inventory.')

@section('content')
    <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900">
        {{ __('Program Manager Operational Workspace Blueprint') }}
    </div>
@endsection
