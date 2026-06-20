@extends('layouts.manager')

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

     <!-- Action Form Box: Register New Physical School -->
    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm space-y-4">
        <h3 class="font-bold text-sm text-gray-800 tracking-tight">Register New School Site</h3>

        <form method="POST" action="{{ route('manager.schools.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">School Name <span class="text-rose-500">*</span></label>
                <input type="text" name="school_name" required placeholder="e.g. Kibera Primary School"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 placeholder-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Location / Sub-County <span class="text-rose-500">*</span></label>
                <input type="text" name="school_location" required placeholder="e.g. Kibera, Nairobi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 placeholder-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Baseline Enrollment (Girls) <span class="text-rose-500">*</span></label>
                <input type="number" name="enrollment" min="0" required placeholder="e.g. 250"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 placeholder-gray-300">
            </div>
            <div class="md:col-span-3 pt-2">
                <button type="submit" class="bg-[#0F766E] hover:bg-[#0D635C] text-white text-xs font-bold px-4 py-2.5 rounded-md transition shadow-sm">
                    Save School Site
                </button>
            </div>
        </form>
    </div>

    <!-- School Directory Registry Table Grid -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
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
                        <td class="px-6 py-4 font-medium">{{ $school->school_location }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-700">{{ number_format($school->enrollment) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $school->coordinators_count }} Linked
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No school sites have been registered in the system directory yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
