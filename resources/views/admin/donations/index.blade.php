@extends('layouts.app')

@section('content')

<h1>Donations</h1>

@if(session('success'))
    <div style="background:#2ecc71;color:white;padding:10px;border-radius:5px;margin-bottom:10px;">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('admin.donations.create') }}" style="margin-bottom:10px;display:inline-block;">
    + Add Donation
</a>

<table width="100%" border="1" cellpadding="10" style="background:white;">
    <tr style="background:#34495e;color:white;">
        <th>Donor</th>
        <th>Pads</th>
        <th>Pledge Date</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    @foreach($donations as $donation)
        <tr>
            <td>{{ $donation->donor?->name ?? 'Unknown' }}</td>
            <td>{{ $donation->pad_count }}</td>
            <td>{{ $donation->pledge_date }}</td>
            <td>{{ $donation->pledge_status }}</td>
            <td>
                <a href="{{ route('admin.donations.show', $donation) }}">View</a> |
                <a href="{{ route('admin.donations.edit', $donation) }}">Edit</a> |
                <form action="{{ route('admin.donations.destroy', $donation) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this donation?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red;background:none;border:none;cursor:pointer;">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

@endsection
