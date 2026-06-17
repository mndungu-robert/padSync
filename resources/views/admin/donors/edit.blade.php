@extends('layouts.app')

@section('content')

<h1>Edit Donor</h1>

@if ($errors->any())
    <div class="alert" style="background:#f8d7da;color:#721c24;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.donors.update', $donor) }}">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ old('name', $donor->name) }}"><br><br>
    <input type="email" name="email" value="{{ old('email', $donor->email) }}"><br><br>
    <input type="text" name="phone" value="{{ old('phone', $donor->phone) }}"><br><br>

    <select name="donor_type">
        <option value="Individual" @selected(old('donor_type', $donor->donor_type) === 'Individual')>Individual</option>
        <option value="Organization" @selected(old('donor_type', $donor->donor_type) === 'Organization')>Organization</option>
    </select><br><br>

    <input type="text" name="organization_name" value="{{ old('organization_name', $donor->organization_name) }}"><br><br>

    <button type="submit">Update</button>
</form>

@endsection
