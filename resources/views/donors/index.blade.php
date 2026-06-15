@extends('layouts.app')

@section('content')

<h1>Donors</h1>

@if(session('success'))
    <div style="background:#2ecc71;color:white;padding:10px;border-radius:5px;margin-bottom:10px;">
        {{ session('success') }}
    </div>
@endif

<a href="{{ route('donors.create') }}" style="margin-bottom:10px;display:inline-block;">
    + Add Donor
</a>

<table width="100%" border="1" cellpadding="10" style="background:white;">
    <tr style="background:#34495e;color:white;">
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Type</th>
        <th>Organization</th>
        <th>Actions</th>
    </tr>

    @foreach($donors as $donor)
        <tr>
            <td>{{ $donor->name }}</td>
            <td>{{ $donor->email }}</td>
            <td>{{ $donor->phone }}</td>
            <td>{{ $donor->donor_type }}</td>
            <td>{{ $donor->organization_name }}</td>

            <td>
                <!-- VIEW -->
                <a href="{{ route('donors.show', $donor->id) }}"
                   style="color:blue;">
                   View
                </a>

                |

                <!-- EDIT -->
                <a href="{{ route('donors.edit', $donor->id) }}"
                   style="color:orange;">
                   Edit
                </a>

                |

                <!-- DELETE -->
                <form action="{{ route('donors.destroy', $donor->id) }}"
                      method="POST"
                      style="display:inline;"
                      onsubmit="return confirm('Delete this donor?')">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            style="color:red;background:none;border:none;cursor:pointer;">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

@endsection