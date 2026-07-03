@extends('layouts.manager')

@section('title', 'Reports Central - ' . config('app.name'))
@section('page_title', 'Reports Central Hub')
@section('page_subtitle', 'Generate, filter, and export system-wide logistical datasets for audits and donor reporting.')

@section('content')
    <!-- Operational Success Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl text-xs font-semibold border border-emerald-200 mb-6">
            🎉 {{ session('success') }}
        </div>
    @endif

    <!-- Data Scope Quick Counters Grid -->
    <div class="grid grid-cols-2 md:grid-cols-7 gap-4">
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Track Sites</div>
            <div class="text-xl font-bold text-slate-800 mt-1">{{ $summary['total_schools'] }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">In Warehouse</div>
            <div class="text-xl font-bold text-slate-800 mt-1">{{ number_format($summary['current_stock_pool']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Pledges</div>
            <div class="text-xl font-bold text-slate-800 mt-1">{{ number_format($summary['cumulative_pledges']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Money Received (KES)</div>
            <div class="text-xl font-bold text-emerald-700 mt-1">{{ number_format((float) $summary['money_received'], 2) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Money Pending (KES)</div>
            <div class="text-xl font-bold text-amber-700 mt-1">{{ number_format((float) $summary['money_pending'], 2) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">In Transit</div>
            <div class="text-xl font-bold text-amber-600 mt-1">{{ number_format($summary['total_dispatched']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm text-center">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Confirmed Recv</div>
            <div class="text-xl font-bold text-emerald-700 mt-1">{{ number_format($summary['total_delivered']) }}</div>
        </div>
    </div>

    <!-- Export Configurations Console Panel Box -->
    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm space-y-4 mt-6 max-w-2xl">
        <h3 class="font-bold text-sm text-gray-800 tracking-tight border-b border-gray-100 pb-3">Configure Export Target</h3>
        
        <form method="POST" action="{{ route('manager.reports.export') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 gap-4">
                <!-- Select Scope Dataset Type Selection Dropdown -->
                <div>
                    <label for="report_type" class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wider">Dataset Module Scope</label>
                    <select id="report_type" name="report_type" required 
                        class="w-full px-3 py-2 bg-slate-50 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 text-gray-700 cursor-pointer">
                        <option value="inventory">Warehouse Inventory Balance Sheets</option>
                        <option value="shortfalls">School Shortfall Deficit Tickets</option>
                        <option value="donations">Cumulative Public Pledges Ledger</option>
                        <option value="money">Money Donation Payment Ledger</option>
                        <option value="distributions">Dispatches and Delivery Tracking Logs</option>
                    </select>
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600">
                Export format is locked to PDF for audit integrity and compliance.
            </div>

            <!-- Download Button Trigger Element Link -->
            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-[#0F766E] hover:bg-[#0D635C] text-white text-xs font-bold px-5 py-2.5 rounded-md transition shadow-sm flex items-center space-x-2">
                    <span>⬇️</span> <span>Compile and Download Report</span>
                </button>
            </div>
        </form>
    </div>
@endsection
