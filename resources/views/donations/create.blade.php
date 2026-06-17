@extends('layouts.app')

@section('title', 'Make a Donation')

@section('content')

<h2>Make a Donation</h2>

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
    </p>

    <p>
        <label>Email</label><br>
        <input type="email" name="email" required>
    </p>

    {{-- <p>
        <label>Phone</label><br>
        <input type="text" name="phone">
    </p> --}}

    <p>
        <label>Donor Type</label><br>
        <select name="donor_type">
            <option value="Individual">Individual</option>
            <option value="Organization">Organization</option>
        </select>
    </p>

    <p>
        <label>Organization Name</label><br>
        <input type="text" name="organization_name">
    </p>

    <p>
        <label>Pads Pledged</label><br>
        <input type="number" name="quantity_pledged" min="1" required>
    </p>

    <button class="btn" type="submit">Submit Donation</button>
    <p> Any donation is appreciated, and we will follow up with you to coordinate delivery. Thank you for your support! </p>
</form>

@endsection