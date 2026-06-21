@extends('layouts.manager')

@section('title', 'Inventory Overview - ' . config('app.name'))
@section('page_title', 'Inventory Overview')
@section('page_subtitle', 'Monitor stock levels, donation activity, and warehouse health.')

@section('content')
    @php
        $inventoryMetrics = array_merge([
            'total_stock' => 0,
            'allocated' => 0,
            'available' => 0,
        ], $metrics ?? []);
        $inventoryDonations = collect($donations ?? []);
    @endphp

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-lg text-xs font-semibold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

      <!-- Real-time Status Metric Aggregation Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Pads in Stock</div>
            <div class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($inventoryMetrics['total_stock']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Allocated (Pending)</div>
            <div class="text-3xl font-bold text-amber-600 mt-2">{{ number_format($inventoryMetrics['allocated']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available to Dispatch</div>
            <div class="text-3xl font-bold text-teal-700 mt-2">{{ number_format($inventoryMetrics['available']) }}</div>
        </div>
    </div>

    <!-- Action Form Box: Log New Donation Batch -->
    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm space-y-4 mt-6">
        <h3 class="font-bold text-sm text-gray-800 tracking-tight">Log New Donation Batch</h3>

        <form method="POST" action="{{ route('manager.inventory.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Donor Name / Organization <span class="text-rose-500">*</span></label>
                <input type="text" name="donor_name" required placeholder="e.g. Safaricom Foundation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 placeholder-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Quantity (pads) <span class="text-rose-500">*</span></label>
                <input type="number" name="quantity_pads" min="1" required placeholder="e.g. 500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 placeholder-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Date Received <span class="text-rose-500">*</span></label>
                <input type="date" name="date_received" required value="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-600 focus:border-teal-600 text-gray-700">
            </div>
            <div class="md:col-span-3 pt-2">
                <button type="submit" class="bg-[#0F766E] hover:bg-[#0D635C] text-white text-xs font-bold px-4 py-2.5 rounded-md transition shadow-sm">
                    Add to Inventory
                </button>
            </div>
        </form>
    </div>

    <!-- Historic Donation Ledger Datatable -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Donation History Ledger</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Donor Source</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Batch Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($inventoryDonations as $donation)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ \Carbon\Carbon::parse($donation->pledge_date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            {{ $donation->donor_name }}
                        </td>
                        <td class="px-6 py-4 font-bold text-teal-600">
                            +{{ number_format($donation->pad_count) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $donation->pledge_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No donation batch logs found in inventory storage records.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
