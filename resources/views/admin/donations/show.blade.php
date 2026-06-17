@extends('layouts.app')

@section('content')

<h1>Donation Details</h1>

<p><strong>Donor:</strong> {{ $donation->donor?->name ?? 'Unknown' }}</p>
<p><strong>Pad Count:</strong> {{ $donation->pad_count }}</p>
<p><strong>Pledge Date:</strong> {{ $donation->pledge_date }}</p>
<p><strong>Expected Delivery:</strong> {{ $donation->expected_delivery_date ?? 'N/A' }}</p>
<p><strong>Status:</strong> {{ $donation->pledge_status }}</p>
<p><strong>Notes:</strong> {{ $donation->notes ?? 'N/A' }}</p>

<p>
    <a href="{{ route('admin.donations.edit', $donation) }}">Edit</a> |
    <a href="{{ route('admin.donations.index') }}">Back to list</a>
</p>

@endsection
