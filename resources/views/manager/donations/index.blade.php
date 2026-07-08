@extends('layouts.manager')

@section('title', 'Donation Pledges Registry - ' . config('app.name'))
@section('page_title', 'Donation Pledges Registry')
@section('page_subtitle', 'Monitor external packet pledges submitted via the public portal site.')

@section('content')
    @php
        $physicalRows = collect($physicalPledges ?? []);
        $moneyRows = collect($moneyDonations ?? []);
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <a href="#physical-donations-table" class="block bg-white border border-gray-200 p-4 rounded-xl shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600" aria-label="Jump to physical donations table">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Physical Donation Pledges</div>
            <div class="text-2xl font-black text-slate-800 mt-2">{{ number_format($physicalRows->count()) }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-1">View table →</div>
        </a>
        <a href="#money-donations-table" class="block bg-white border border-gray-200 p-4 rounded-xl shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-600" aria-label="Jump to money donations table">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Money Donation Transactions</div>
            <div class="text-2xl font-black text-slate-800 mt-2">{{ number_format($moneyRows->count()) }}</div>
            <div class="text-[10px] font-semibold text-gray-500 mt-1">View table →</div>
        </a>
    </div>

    <!-- Physical Donation Table -->
    <div id="physical-donations-table" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Physical Donation Pledges (Pads)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Donor Profile Details</th>
                        <th class="px-6 py-3.5">Classification Type</th>
                        <th class="px-6 py-3.5 text-center">Packs Pledged</th>
                        <th class="px-6 py-3.5">Pledge Logging Date</th>
                        <th class="px-6 py-3.5 text-right">Fulfillment State</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($physicalRows as $pledge)
                    @php
                        $fulfillmentState = $pledge->fulfillment_state ?? ($pledge->fulfillment_date ? 'Fully Received' : 'Pledged');
                    @endphp
                    <tr class="hover:bg-gray-50/40 transition">
                        <!-- Profile Metadata -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $pledge->donor_name }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $pledge->donor_email }}</div>
                        </td>
                        
                        <!-- Classification -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/60">
                                {{ $pledge->donor_type }}
                            </span>
                        </td>
                        
                        <!-- Quantity Metrics -->
                        <td class="px-6 py-4 text-center font-bold text-slate-800">
                            {{ number_format($pledge->pad_count) }}
                        </td>

                        <!-- Logging Date -->
                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ \Carbon\Carbon::parse($pledge->pledge_date)->format('M d, Y') }}
                        </td>
                        
                        <!-- Fulfillment State Badge -->
                        <td class="px-6 py-4 text-right">
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border
                                {{ $fulfillmentState === 'Fully Received' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                {{ $fulfillmentState }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if(($pledge->contribution_type ?? 'Donate Pads') === 'Donate Pads' && $fulfillmentState === 'Pledged' && (($pledge->payment_status ?? 'Successful') === 'Successful' || ($pledge->payment_status ?? '') === 'Not Required'))
                                <form method="POST" action="{{ route('manager.donations.receive', $pledge) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-md text-[11px] font-bold bg-[#0F766E] text-white hover:bg-[#0D635C] transition">
                                        Mark Received
                                    </button>
                                </form>
                            @elseif(($pledge->contribution_type ?? 'Donate Pads') !== 'Donate Pads')
                                <span class="text-[11px] text-gray-500 font-semibold">No Inventory Action</span>
                            @elseif($fulfillmentState === 'Pledged')
                                <span class="text-[11px] text-amber-600 font-semibold">Awaiting Payment</span>
                            @else
                                <span class="text-[11px] text-gray-400 font-semibold">Received</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">No physical donation pledges have been logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Money Donation Table -->
    <div id="money-donations-table" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Money Donation Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Donor Profile Details</th>
                        <th class="px-6 py-3.5">Classification Type</th>
                        <th class="px-6 py-3.5 text-center">Amount (KES)</th>
                        <th class="px-6 py-3.5 text-center">Payment Status</th>
                        <th class="px-6 py-3.5 text-center">Receipt Ref</th>
                        <th class="px-6 py-3.5">Logged Date</th>
                        <th class="px-6 py-3.5">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($moneyRows as $donation)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $donation->donor_name }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $donation->donor_email }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/60">
                                {{ $donation->donor_type }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center font-bold text-slate-800">
                            {{ number_format((float) ($donation->amount_kes ?? 0), 2) }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                                $paymentState = $donation->payment_status ?? 'Failed';
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border {{ $paymentState === 'Successful' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                {{ $paymentState }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center text-xs font-mono text-gray-500">
                            {{ $donation->payment_reference ?? 'N/A' }}
                        </td>

                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ \Carbon\Carbon::parse($donation->pledge_date)->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4 text-xs font-medium text-gray-400">
                            {{ $donation->paid_at ? \Carbon\Carbon::parse($donation->paid_at)->format('M d, Y H:i') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-400 font-medium">No money donation transactions have been logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
