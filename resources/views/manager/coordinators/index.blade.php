@extends('layouts.manager', ['active' => 'coordinators'])

@section('title', 'Coordinator Requests - PadSync')
@section('page_title', 'Coordinator Requests')
@section('page_subtitle', 'Approve, reject, and manage school coordinator assignments.')

@section('content')
    <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900">
        {{ __('Pending coordinator approvals and assignment workflows will be added here.') }}
    </div>
@endsection
