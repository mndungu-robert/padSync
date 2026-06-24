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

<!-- HERO STATS SECTION -->
<div style="text-align:center; padding: 30px 10px; background: linear-gradient(135deg, #4f46e5, #6d28d9); color:white; border-radius:12px;">

    <h2 style="font-size: 28px; font-weight: bold; margin-bottom: 10px;">
        Every Girl Deserves to Stay in School
    </h2>

    <p style="max-width: 600px; margin: 0 auto 25px; font-size: 14px; opacity: 0.9;">
        The {{ config('app.name') }} Programme distributes sanitary towel packets to sponsored schools. Your pledge closes the gap.
    </p>

    <!-- STATS -->
    <div style="display:flex; justify-content:center; gap:40px; flex-wrap:wrap; margin-top:20px;">

        <div>
            <h3 style="font-size:36px; font-weight:bold;">{{ number_format($impact['schools_supported']) }}</h3>
            <p style="font-size:13px; opacity:0.9;">Schools Supported</p>
        </div>

        <div>
            <h3 style="font-size:36px; font-weight:bold;">{{ number_format($impact['girls_enrolled']) }}</h3>
            <p style="font-size:13px; opacity:0.9;">Girls Supported</p>
        </div>

        <div>
            <h3 style="font-size:36px; font-weight:bold; color:#f472b6;">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</h3>
            <p style="font-size:13px; opacity:0.9;">Packets Needed Monthly</p>
        </div>

    </div>
</div>

<!-- FORM SECTION -->
<div class="container" style="margin-top: 25px;">

<h2 style="margin-bottom: 15px;">Make a Donation</h2>

@if(session('success'))
    <div class="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert" style="background:#f8d7da;color:#721c24;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('donate.store') }}">

    @csrf

    <p>
        <label>Name</label><br>
        <input type="text" name="name" required>
        <small style="color:#6b7280; display:block; margin-top:4px;">If donor type is Organization, enter the organization name here.</small>
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    <p>
        <label>Donor Type</label><br>
        <select name="donor_type" required>
            <option value="Individual" {{ old('donor_type') === 'Individual' ? 'selected' : '' }}>Individual</option>
            <option value="Organization" {{ old('donor_type') === 'Organization' ? 'selected' : '' }}>Organization</option>
        </select>
    </p>

    <p>
        <label>Packets Pledged</label><br>
        <input type="number" name="quantity_pledged" min="1" required>
    </p>

    <button class="btn" type="submit">Submit Donation</button>

    <p style="margin-top:10px; font-size:13px; color:#6b7280;">
        Any donation is appreciated, and we will follow up with you to coordinate delivery. Thank you for your support!
    </p>

</form>

</div>

@endsection