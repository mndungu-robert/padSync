@extends('layouts.manager', ['active' => 'inventory'])

@section('title', 'Inventory Overview - PadSync')
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
            <div class="text-3xl font-bold text-emerald-700 mt-2">{{ number_format($inventoryMetrics['available']) }}</div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
        <h3 class="font-bold text-sm text-gray-800 tracking-tight">Donation Intake</h3>
        <p class="mt-2 text-sm text-gray-500">
            Donation logging is being aligned with the manager workflow. This view currently provides read-only stock and history visibility.
        </p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Donation History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Donor</th>
                        <th class="px-6 py-3">Quantity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($inventoryDonations as $donation)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 text-xs font-medium text-gray-400">
                                {{ optional($donation->pledge_date ? \Illuminate\Support\Carbon::parse($donation->pledge_date) : null)->format('M d, Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $donation->donor_name ?? $donation->organization_name ?? 'Unknown donor' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                +{{ number_format((int) ($donation->pad_count ?? 0)) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 font-medium">No donation history available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
