@extends('layouts.manager')

@section('title', 'Program Manager Dashboard - PadSync')
@section('page_title', 'Program Manager Dashboard')
@section('page_subtitle', 'High-level operational visibility for schools, coordinators, and inventory.')

@section('content')
    @php
        $dashboardMetrics = array_merge([
            'available_stock' => 0,
            'reorder_threshold' => 100,
            'schools_count' => 0,
            'active_shortfalls' => 0,
            'pending_profiles' => 0,
        ], $metrics ?? []);
        $criticalNeedRows = collect($criticalNeeds ?? []);
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5">
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available Stock</div>
            <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($dashboardMetrics['available_stock']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reorder Threshold</div>
            <div class="text-2xl font-bold text-amber-700 mt-2">{{ number_format($dashboardMetrics['reorder_threshold']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Registered Schools</div>
            <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($dashboardMetrics['schools_count']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Shortfalls</div>
            <div class="text-2xl font-bold text-rose-700 mt-2">{{ number_format($dashboardMetrics['active_shortfalls']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Coordinators</div>
            <div class="text-2xl font-bold text-teal-700 mt-2">{{ number_format($dashboardMetrics['pending_profiles']) }}</div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Top Critical Shortfall Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">School</th>
                        <th class="px-6 py-3">Required</th>
                        <th class="px-6 py-3">Available</th>
                        <th class="px-6 py-3">Shortfall</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($criticalNeedRows as $row)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $row->school->school_name ?? 'Unknown school' }}</td>
                            <td class="px-6 py-4">{{ number_format((int) $row->required_pads) }}</td>
                            <td class="px-6 py-4">{{ number_format((int) $row->available_pads) }}</td>
                            <td class="px-6 py-4 font-bold text-rose-700">{{ number_format((int) $row->shortfall) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No submitted shortfall reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
