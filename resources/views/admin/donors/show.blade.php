@extends('layouts.app')

@section('content')

<h1>Donor Details</h1>

<p><strong>Name:</strong> {{ $donor->name }}</p>
<p><strong>Email:</strong> {{ $donor->email }}</p>
<p><strong>Pad Count:</strong> {{ $donor->pad_count }}</p>
<p><strong>Type:</strong> {{ $donor->donor_type }}</p>
<p><strong>Organization:</strong> {{ $donor->organization_name ?? 'N/A' }}</p>

<p>
    <a href="{{ route('admin.donors.edit', $donor) }}">Edit</a> |
    <a href="{{ route('admin.donors.index') }}">Back to list</a>
</p>

@endsection
