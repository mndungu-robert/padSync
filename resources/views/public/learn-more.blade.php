@extends('layouts.app')

@section('title', 'Learn More - ' . config('app.name'))

@section('content')
@php
    $impact = $stats ?? [
        'schools_supported' => 0,
        'girls_enrolled' => 0,
        'packets_needed_monthly' => 0,
        'pads_needed_monthly' => 0,
    ];
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap');

    .public-page {
        font-family: 'Manrope', sans-serif;
    }

    .public-serif {
        font-family: 'Fraunces', serif;
    }
</style>

<div class="public-page" style="margin: -4px;">
    <div style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border:1px solid #c7d2fe; border-radius:16px; padding:24px; margin-bottom:16px; box-shadow:0 8px 16px rgba(79,70,229,.1);">
        <p style="margin:0 0 8px; display:inline-block; padding:4px 10px; border-radius:999px; background:#eef2ff; border:1px solid #c7d2fe; color:#4338ca; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.08em;">Program Story</p>
        <h2 class="public-serif" style="margin:0; font-size:34px; color:#312e81;">About {{ config('app.name') }}</h2>
        <p style="margin-top:10px; color:#4338ca; max-width:760px; line-height:1.7;">
            {{ config('app.name') }} helps schools, coordinators, and program managers track sanitary towel packet needs and distribution in one transparent workflow.
            The goal is simple: reduce absenteeism by ensuring girls have reliable monthly access.
        </p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:16px;">
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 6px 12px rgba(15,23,42,.03);">
            <div style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:700;">Schools Supported</div>
            <div style="font-size:30px; color:#1f2937; font-weight:800; margin-top:4px;">{{ number_format($impact['schools_supported']) }}</div>
        </div>
        <div style="background:white; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 6px 12px rgba(15,23,42,.03);">
            <div style="font-size:12px; color:#6b7280; text-transform:uppercase; font-weight:700;">Girls Supported</div>
            <div style="font-size:30px; color:#1f2937; font-weight:800; margin-top:4px;">{{ number_format($impact['girls_enrolled']) }}</div>
        </div>
        <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:12px; padding:16px; box-shadow:0 6px 12px rgba(15,23,42,.03);">
            <div style="font-size:12px; color:#4338ca; text-transform:uppercase; font-weight:700;">Packets Needed Monthly</div>
            <div style="font-size:30px; color:#4338ca; font-weight:800; margin-top:4px;">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</div>
        </div>
    </div>

    <div style="background:white; border:1px solid #e5e7eb; border-radius:16px; padding:20px; box-shadow:0 8px 16px rgba(15,23,42,.04);">
        <h3 class="public-serif" style="margin:0 0 10px; color:#111827; font-size:28px;">How It Works</h3>
        <ol style="margin:0; padding-left:18px; color:#374151; line-height:1.8;">
            <li>Coordinators submit monthly enrollments and shortfall reports.</li>
            <li>Program managers dispatch available stock to schools with active need.</li>
            <li>Coordinators confirm receipt, closing the distribution loop.</li>
        </ol>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('donate.form') }}" class="btn" style="font-weight:800;">Donate Now</a>
            <a href="{{ url('/') }}" style="padding:10px 14px; border:1px solid #c7d2fe; border-radius:8px; text-decoration:none; color:#4338ca; font-weight:600;">Back Home</a>
        </div>
    </div>
</div>
@endsection
