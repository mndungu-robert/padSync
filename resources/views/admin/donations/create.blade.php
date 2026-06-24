@extends('layouts.app')

@section('content')

<h1>Add Donation</h1>

@if ($errors->any())
    <div class="alert" style="background:#f8d7da;color:#721c24;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.donations.store') }}">
    @csrf

    <label>Donor</label><br>
    <select name="donor_id" required>
        <option value="">Select donor</option>
        @foreach ($donors as $donor)
            <option value="{{ $donor->id }}" @selected((int) old('donor_id') === $donor->id)>{{ $donor->name }}</option>
        @endforeach
    </select><br><br>

    <label>Packet Count</label><br>
    <input type="number" name="pad_count" min="1" value="{{ old('pad_count') }}" required><br><br>

    <label>Pledge Date</label><br>
    <input type="date" name="pledge_date" value="{{ old('pledge_date') }}" required><br><br>

    <label>Expected Delivery Date</label><br>
    <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}"><br><br>

    <label>Notes</label><br>
    <textarea name="notes" rows="3">{{ old('notes') }}</textarea><br><br>

    <button type="submit">Save Donation</button>
</form>

@endsection
