@extends('layouts.app')

@section('content')

<h1>Add Donor</h1>

<form method="POST" action="{{ route('donors.store') }}">
    @csrf

    <label>Name</label><br>
    <input type="text" name="name"><br><br>

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Donor Type</label><br>
    <select name="donor_type">
        <option>Individual</option>
        <option>Organization</option>
    </select><br><br>

    <label>Organization Name</label><br>
    <input type="text" name="organization_name"><br><br>

    <button type="submit">Save Donor</button>
</form>

@endsection