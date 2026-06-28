@extends('layouts.app')

@section('title', 'Make a Donation')

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
    <div style="background:linear-gradient(135deg, #eef2ff, #e0e7ff); border:1px solid #c7d2fe; color:#312e81; border-radius:16px; padding:24px 18px; box-shadow:0 10px 20px rgba(79,70,229,.12);">
        <p style="margin:0 0 10px; display:inline-block; padding:4px 10px; border-radius:999px; background:#eef2ff; border:1px solid #c7d2fe; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase;">Monthly Program Need</p>
        <h2 class="public-serif" style="font-size:34px; line-height:1.15; margin:0;">Every Packet Keeps A Girl Learning</h2>
        <p style="margin:12px 0 0; max-width:640px; font-size:14px; color:#4338ca;">Your support helps us meet baseline monthly requirements before shortages disrupt school attendance.</p>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-top:18px;">
            <div style="border:1px solid #dbeafe; background:#ffffff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700;">Schools Supported</div>
                <div style="font-size:30px; color:#1f2937; font-weight:900; margin-top:4px;">{{ number_format($impact['schools_supported']) }}</div>
            </div>
            <div style="border:1px solid #dbeafe; background:#ffffff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; font-weight:700;">Girls Supported</div>
                <div style="font-size:30px; color:#1f2937; font-weight:900; margin-top:4px;">{{ number_format($impact['girls_enrolled']) }}</div>
            </div>
            <div style="border:1px solid #c7d2fe; background:#eef2ff; border-radius:12px; padding:12px;">
                <div style="font-size:11px; text-transform:uppercase; color:#4338ca; font-weight:700;">Packets Needed Monthly</div>
                <div style="font-size:30px; font-weight:900; margin-top:4px; color:#4338ca;">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</div>
            </div>
        </div>
    </div>

    <div style="margin-top:16px; border:1px solid #e0e7ff; background:#ffffff; border-radius:16px; padding:18px; box-shadow:0 8px 16px rgba(15,23,42,.04);">
        <h3 class="public-serif" style="margin:0; font-size:28px; color:#312e81;">Make a Donation</h3>
        <p style="margin:8px 0 0; color:#4338ca; font-size:13px;">Choose donor type and pledge packet quantity for this cycle.</p>

        @if(session('success'))
            <div class="alert" style="margin-top:14px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert" style="margin-top:14px; background:#fef2f2; color:#991b1b; border-left-color:#ef4444;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('donate.store') }}" style="margin-top:14px;">
            @csrf

            <p>
                <label style="font-weight:700; color:#3730a3;">Name</label><br>
                <input type="text" name="name" value="{{ old('name') }}" required>
                <small style="color:#6b7280; display:block; margin-top:4px;">If donor type is Organization, enter the organization name here.</small>
            </p>

            <p>
                <label style="font-weight:700; color:#3730a3;">Email</label><br>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </p>

            <p>
                <label style="font-weight:700; color:#3730a3;">Donor Type</label><br>
                <select name="donor_type" required>
                    <option value="Individual" {{ old('donor_type') === 'Individual' ? 'selected' : '' }}>Individual</option>
                    <option value="Organization" {{ old('donor_type') === 'Organization' ? 'selected' : '' }}>Organization</option>
                </select>
            </p>

            <p>
                <label style="font-weight:700; color:#3730a3;">Packets Pledged</label><br>
                <input type="number" name="quantity_pledged" value="{{ old('quantity_pledged') }}" min="1" required>
            </p>

            <button class="btn" type="submit" style="font-weight:800;">Submit Donation</button>

            <p style="margin-top:10px; font-size:13px; color:#6b7280;">
                Any donation is appreciated, and we will follow up with you to coordinate delivery. Thank you for your support!
            </p>
        </form>
    </div>
</div>

@endsection