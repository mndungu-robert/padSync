@extends('layouts.app')

@section('content')

<h1>Edit Donor</h1>

<form method="POST" action="{{ route('donors.update', $donor->id) }}">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $donor->name }}"><br><br>
    <input type="email" name="email" value="{{ $donor->email }}"><br><br>
    {{-- <input type="text" name="phone" value="{{ $donor->phone }}"><br><br> --}}

    <select name="donor_type">
        <option {{ $donor->donor_type == 'Individual' ? 'selected' : '' }}>Individual</option>
        <option {{ $donor->donor_type == 'Organization' ? 'selected' : '' }}>Organization</option>
    </select><br><br>

    <input type="text" name="organization_name" value="{{ $donor->organization_name }}"><br><br>

    <button type="submit">Update</button>
</form>

@endsection
