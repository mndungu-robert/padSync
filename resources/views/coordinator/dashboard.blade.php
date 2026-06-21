@extends('layouts.coordinator', ['active' => 'dashboard'])

@section('title', 'Coordinator Dashboard - PadSync')
@section('page_title', 'Coordinator Dashboard')
@section('page_subtitle', 'Daily field operations and reporting overview.')

@section('content')
    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 p-3 rounded-lg text-xs font-semibold border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        @if($school)
            <h3 class="text-sm font-bold text-gray-800">Assigned School</h3>
            <p class="text-base font-semibold text-indigo-700 mt-1">{{ $school->school_name }}</p>
            <p class="text-xs text-gray-500">{{ $school->school_location }}</p>
        @else
            <h3 class="text-sm font-bold text-rose-700">No School Assignment</h3>
            <p class="text-xs text-rose-600 mt-1">Ask a Program Manager to assign your account to a school before submitting records.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-gray-400 font-bold">Enrollment Logs</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($metrics['enrollment_logs']) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-gray-400 font-bold">Latest Girls Count</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($metrics['latest_enrollment']) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-gray-400 font-bold">Open Shortfalls</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($metrics['open_shortfalls']) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-gray-400 font-bold">Total Reported Shortfall</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($metrics['total_shortfall']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">Recent Enrollment Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">Academic Year</th>
                            <th class="px-4 py-3 text-left">Month</th>
                            <th class="px-4 py-3 text-left">Girls</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $enrollment->academic_year }}</td>
                                <td class="px-4 py-3">{{ $enrollment->month ?: 'N/A' }}</td>
                                <td class="px-4 py-3">{{ number_format($enrollment->girl_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-400">No enrollment logs submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">Recent Shortfall Reports</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Shortfall</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($recentShortfalls as $report)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ \Illuminate\Support\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3">{{ number_format($report->shortfall) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $report->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-400">No shortfall reports submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
