@extends('layouts.app')

@section('title', 'Learn More - ' . config('app.name'))

@section('content')
@php
    $impact = $stats ?? ['schools_supported' => 0, 'girls_enrolled' => 0, 'pads_still_needed' => 0];
@endphp

<div style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border:1px solid #c7d2fe; border-radius:12px; padding:24px; margin-bottom:20px;">
    <h2 style="margin:0; font-size:28px; color:#312e81;">About {{ config('app.name') }}</h2>
    <p style="margin-top:10px; color:#4338ca; max-width:760px;">
        {{ config('app.name') }} helps schools, coordinators, and program managers track sanitary pad needs and distribution in one transparent workflow.
        The goal is simple: reduce absenteeism by ensuring girls have reliable monthly access.
    </p>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:20px;">
    <div style="background:white; border:1px solid #e5e7eb; border-radius:10px; padding:16px;">
        <div style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:700;">Schools Supported</div>
        <div style="font-size:30px; color:#1f2937; font-weight:800; margin-top:4px;">{{ number_format($impact['schools_supported']) }}</div>
    </div>
    <div style="background:white; border:1px solid #e5e7eb; border-radius:10px; padding:16px;">
        <div style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:700;">Girls Enrolled</div>
        <div style="font-size:30px; color:#1f2937; font-weight:800; margin-top:4px;">{{ number_format($impact['girls_enrolled']) }}</div>
    </div>
    <div style="background:white; border:1px solid #e5e7eb; border-radius:10px; padding:16px;">
        <div style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:700;">Pads Still Needed</div>
        <div style="font-size:30px; color:#be185d; font-weight:800; margin-top:4px;">{{ number_format($impact['pads_still_needed']) }}</div>
    </div>
</div>

<div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
    <h3 style="margin:0 0 10px; color:#111827;">How It Works</h3>
    <ol style="margin:0; padding-left:18px; color:#374151; line-height:1.8;">
        <li>Coordinators submit monthly enrollments and shortfall reports.</li>
        <li>Program managers dispatch available stock to schools with active need.</li>
        <li>Coordinators confirm receipt, closing the distribution loop.</li>
    </ol>

    <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('donate.form') }}" class="btn">Donate Now</a>
        <a href="{{ url('/') }}" style="padding:10px 14px; border:1px solid #c7d2fe; border-radius:8px; text-decoration:none; color:#4338ca; font-weight:600;">Back Home</a>
    </div>
</div>
@endsection
