@extends('layouts.manager', ['active' => 'coordinators'])

@section('title', 'Coordinator Requests - PadSync')
@section('page_title', 'Coordinator Requests')
@section('page_subtitle', 'Approve, reject, and manage school coordinator assignments.')

@section('content')
     <!-- Operational Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-xs font-semibold border border-emerald-200 mb-6">
            🎉 {{ session('success') }}
        </div>
    @endif

    <!-- Registration Queue Table Layout -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-sm text-gray-800">Coordinator Registration Queue</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Coordinator Details</th>
                        <th class="px-6 py-3.5">Assigned School Site</th>
                        <th class="px-6 py-3.5">Date Registered</th>
                        <th class="px-6 py-3.5">Current Status</th>
                        <th class="px-6 py-3.5 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($coordinators as $coordinator)
                    <tr class="hover:bg-gray-50/40 transition">
                        <!-- Profile Metadata -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $coordinator->name }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $coordinator->email }} | &#64;{{ $coordinator->username }}</div>
                        </td>
                        
                        <!-- Linked Relationship Model Value -->
                        <td class="px-6 py-4">
                            @if($coordinator->school)
                                <div class="font-semibold text-slate-700">{{ $coordinator->school->school_name }}</div>
                                <div class="text-xs text-gray-400">{{ $coordinator->school->school_location }}</div>
                            @else
                                <span class="text-rose-500 font-medium italic text-xs">No Site Linked</span>
                            @endif
                        </td>
                        
                        <!-- Date Info -->
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ $coordinator->created_at->format('M d, Y') }} <br>
                            <span class="text-[10px] text-gray-300">{{ $coordinator->created_at->diffForHumans() }}</span>
                        </td>
                        
                        <!-- Workflow Status Badge Tags -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border
                                {{ $coordinator->status === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                {{ $coordinator->status === 'Pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                {{ $coordinator->status === 'Rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                            ">
                                {{ $coordinator->status }}
                            </span>
                        </td>
                        
                        <!-- Management Form Submissions Triggers -->
                        <td class="px-6 py-4 text-center">
                            @if($coordinator->status === 'Pending')
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Approve Form -->
                                    <form method="POST" action="{{ route('manager.coordinators.status', $coordinator->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Approved">
                                        <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold px-3 py-1.5 rounded transition shadow-sm">
                                            Approve
                                        </button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form method="POST" action="{{ route('manager.coordinators.status', $coordinator->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="Rejected">
                                        <button type="submit" class="bg-white hover:bg-rose-50 border border-gray-200 text-rose-600 text-xs font-bold px-3 py-1.5 rounded transition">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-300 font-medium italic">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No school site coordinator accounts are present in the system database logs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection