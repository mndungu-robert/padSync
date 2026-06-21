@extends('layouts.app')

@section('content')

<h1>Add Donor</h1>

@if ($errors->any())
    <div class="alert" style="background:#f8d7da;color:#721c24;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.donors.store') }}">
    @csrf

    <label>Name</label><br>
    <input type="text" name="name" value="{{ old('name') }}"><br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ old('email') }}"><br><br>

    <label>Pad Count</label><br>
    <input type="number" name="pad_count" min="0" value="{{ old('pad_count', 0) }}"><br><br>

    <label>Donor Type</label><br>
    <select name="donor_type">
        <option value="Individual" @selected(old('donor_type') === 'Individual')>Individual</option>
        <option value="Organization" @selected(old('donor_type') === 'Organization')>Organization</option>
    </select><br><br>

    <label>Organization Name</label><br>
    <input type="text" name="organization_name" value="{{ old('organization_name') }}"><br><br>

    <button type="submit">Save Donor</button>
</form>

@endsection
