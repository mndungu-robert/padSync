@extends('layouts.coordinator', ['active' => 'enrollments'])

@section('title', 'Enrollment Logs - PadSync')
@section('page_title', 'Enrollment Logs')
@section('page_subtitle', 'Submit and review school enrollment updates.')

@section('content')
    <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-lg p-6 text-gray-900">
        {{ __('Monthly student enrollment logs will be added here.') }}
    </div>
@endsection
