@extends('layouts.manager')

@section('title', 'Donation Pledges Registry - ' . config('app.name'))
@section('page_title', 'Donation Pledges Registry')
@section('page_subtitle', 'Monitor external pad pledges submitted via the public portal site.')

@section('content')
    <!-- Donation Pledges Registry Grid Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Public Donation Pledges Log</h3>
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
                    @forelse($pledges as $pledge)
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
                            @if($fulfillmentState === 'Pledged')
                                <form method="POST" action="{{ route('manager.donations.receive', $pledge) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-md text-[11px] font-bold bg-[#0F766E] text-white hover:bg-[#0D635C] transition">
                                        Mark Received
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] text-gray-400 font-semibold">Received</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">No donation pledges have been logged from the public portal page yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
