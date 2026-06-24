@extends('layouts.manager')

@section('title', 'Allocation Engine - ' . config('app.name'))
@section('page_title', 'Live Allocation & Dispatches')
@section('page_subtitle', 'Review dynamic monthly school shortfalls and authorize sanitary towel packet dispatches.')

@section('content')
    <!-- Operational Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-xs font-semibold border border-emerald-200 mb-6">
            🎉 {{ session('success') }}
        </div>
    @endif

    @error('stock_error')
        <div class="bg-rose-50 text-rose-800 p-4 rounded-xl text-xs font-semibold border border-rose-200 mb-6">
            ⚠️ {{ $message }}
        </div>
    @enderror

    <!-- Current Available Stock Bar Status Node Info Component -->
    <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Central Warehouse Pool Balance</div>
            <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($availableStock) }} <span class="text-sm text-gray-400 font-medium">packets currently available</span></div>
        </div>
        <span class="inline-flex px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $availableStock > 500 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
            {{ $availableStock > 500 ? 'Stock Levels Secure' : 'Low Stock Warning' }}
        </span>
    </div>

    <!-- Active Shortfall Request Queue Lists -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Pending School Shortfall Queue</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">School Name</th>
                        <th class="px-6 py-3.5">Required Packets</th>
                        <th class="px-6 py-3.5">Available On-Site Packets</th>
                        <th class="px-6 py-3.5">Calculated Shortfall Gap</th>
                        <th class="px-6 py-3.5">Planned Dispatch (+20)</th>
                        <th class="px-6 py-3.5 text-center">Action Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($pendingReports as $report)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $report->school->school_name }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">Report Date: {{ \Carbon\Carbon::parse($report->report_date)->format('M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 font-semibold">{{ number_format($report->required_pads) }}</td>
                        <td class="px-6 py-4 font-medium text-gray-400">{{ number_format($report->available_pads) }}</td>
                        <td class="px-6 py-4 font-bold text-rose-600">
                            {{ number_format($report->shortfall) }} packets
                        </td>
                        <td class="px-6 py-4 font-bold text-teal-700">
                            {{ number_format($report->dispatch_quantity) }} packets
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form method="POST" action="{{ route('manager.distributions.store') }}">
                                @csrf
                                <input type="hidden" name="report_id" value="{{ $report->report_id }}">
                                <button type="submit" 
                                    class="text-white text-xs font-bold px-4 py-2 rounded-md transition shadow-sm
                                    {{ $availableStock >= $report->dispatch_quantity ? 'bg-teal-700 hover:bg-teal-800' : 'bg-gray-300 cursor-not-allowed' }}"
                                    {{ $availableStock >= $report->dispatch_quantity ? '' : 'disabled' }}>
                                    Authorize Dispatch
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">All school shortfall tickets are clear. No pending allocation items.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historical Dispatch Activity Registry Log -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Recent Dispatch Tracking History Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Dispatch Date</th>
                        <th class="px-6 py-3">Recipient School Site</th>
                        <th class="px-6 py-3">Quantity Sent</th>
                        <th class="px-6 py-3">Delivery Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($dispatches as $dispatch)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ \Carbon\Carbon::parse($dispatch->distribution_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ $dispatch->school->school_name }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-teal-600">
                            {{ number_format($dispatch->quantity_distributed) }} packets
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border
                                {{ $dispatch->status === 'Received' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                {{ $dispatch->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No previous dispatch tracking history logs are recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
