@extends('layouts.coordinator', ['active' => 'dashboard'])

@section('title', 'Coordinator Dashboard - PadSync')
@section('page_title', 'Coordinator Dashboard')
@section('page_subtitle', 'Daily field operations and reporting overview.')

@section('content')
    <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900">
        {{ __('Coordinator Operational Workspace Blueprint') }}
    </div>
@endsection
