@extends('layouts.coordinator', ['active' => 'shortfalls'])

@section('title', 'Shortfall Reports - PadSync')
@section('page_title', 'Shortfall Reports')
@section('page_subtitle', 'Track gaps and request replenishment by school and cycle.')

@section('content')
    <div class="bg-indigo-50 text-indigo-800 p-3 rounded-lg text-xs font-semibold border border-indigo-200">
        Notice: Shortfall is calculated as Required Pads minus (Available Pads + Govt Pads Received).
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-lg text-xs font-semibold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 p-3 rounded-lg text-xs font-semibold border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-3 rounded-lg text-xs border border-rose-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800">Submit Shortfall Report</h3>

        @if($school)
            <p class="text-xs text-gray-500 mt-1">School: <span class="font-semibold text-gray-700">{{ $school->school_name }}</span></p>

            <form method="POST" action="{{ route('coordinator.shortfalls.store') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Report Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="report_date" value="{{ old('report_date', now()->toDateString()) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Required Pads <span class="text-rose-500">*</span></label>
                    <input type="number" name="required_pads" value="{{ old('required_pads') }}" min="0" required placeholder="e.g. 1200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Available Pads <span class="text-rose-500">*</span></label>
                    <input type="number" name="available_pads" value="{{ old('available_pads') }}" min="0" required placeholder="e.g. 300"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Govt Pads Received <span class="text-rose-500">*</span></label>
                    <input type="number" name="government_pads_received" value="{{ old('government_pads_received') }}" min="0" required placeholder="e.g. 250"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        @foreach(['Draft', 'Submitted', 'Dispatched', 'Received'] as $status)
                            <option value="{{ $status }}" @selected(old('status', 'Submitted') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 xl:col-span-4 pt-1">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold px-4 py-2.5 rounded-md transition shadow-sm">
                        Submit Shortfall Report
                    </button>
                </div>
            </form>
        @else
            <p class="text-xs text-rose-600 mt-2">Your account is not linked to a school yet. Contact a Program Manager.</p>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Shortfall Report History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Required</th>
                        <th class="px-6 py-3">Available</th>
                        <th class="px-6 py-3">Govt Received</th>
                        <th class="px-6 py-3">Total Available</th>
                        <th class="px-6 py-3">Shortfall</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ \Illuminate\Support\Carbon::parse($report->report_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4">{{ number_format($report->required_pads) }}</td>
                            <td class="px-6 py-4">{{ number_format($report->available_pads) }}</td>
                            <td class="px-6 py-4">{{ number_format($report->government_pads_received ?? 0) }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format((int) $report->available_pads + (int) ($report->government_pads_received ?? 0)) }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($report->shortfall) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $report->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400 font-medium">No shortfall reports submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
