@extends('layouts.manager', ['active' => 'schools'])

@section('title', 'Schools Directory - PadSync')
@section('page_title', 'Schools Directory')
@section('page_subtitle', 'Track onboarded schools and their operational status.')

@section('content')
    @php
        $schoolList = collect($schools ?? []);
    @endphp

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-lg text-xs font-semibold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
        <h3 class="font-bold text-sm text-gray-800 tracking-tight">School Registration</h3>
        <p class="mt-2 text-sm text-gray-500">
            School onboarding is being aligned with the manager workflow. This screen currently provides read-only visibility for registered schools.
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Registered School Sites</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">School Name</th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3">Baseline Girls</th>
                        <th class="px-6 py-3">Assigned Coordinators</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($schoolList as $school)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-mono text-gray-400">#{{ $school->school_id }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $school->school_name }}</td>
                            <td class="px-6 py-4 font-medium">{{ $school->school_location ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-700">{{ number_format((int) ($school->enrollment ?? 0)) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ (int) ($school->coordinators_count ?? 0) }} Linked
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No school sites have been registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
