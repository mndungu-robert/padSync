@extends('layouts.coordinator', ['active' => 'distributions'])

@section('title', 'Distributions - ' . config('app.name'))
@section('page_title', 'Distributions')
@section('page_subtitle', 'Confirm receipt of pads dispatched to your school.')

@section('content')
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

    <div class="bg-indigo-50 text-indigo-800 p-3 rounded-lg text-xs font-semibold border border-indigo-200">
        Confirm each pending or dispatched batch once it is physically received at your school site.
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800">School Assignment</h3>
        @if($school)
            <p class="text-xs text-gray-500 mt-1">
                School: <span class="font-semibold text-gray-700">{{ $school->school_name }}</span>
                <span class="text-gray-400">|</span>
                {{ $school->school_location }}
            </p>
        @else
            <p class="text-xs text-rose-600 mt-2">Your account is not linked to a school yet. Contact a Program Manager.</p>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Pending Dispatches</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Dispatch Date</th>
                        <th class="px-6 py-3">Quantity Dispatched</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($pendingDispatches as $dispatch)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ \Illuminate\Support\Carbon::parse($dispatch->distribution_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($dispatch->quantity_distributed) }} pads</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $dispatch->status === 'Pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                    {{ $dispatch->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" action="{{ route('coordinator.distributions.confirm', $dispatch) }}">
                                    @csrf
                                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold px-4 py-2 rounded-md transition shadow-sm">
                                        Confirm Received
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No pending dispatches to confirm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Confirmed Receipts</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Dispatch Date</th>
                        <th class="px-6 py-3">Quantity Received</th>
                        <th class="px-6 py-3">Confirmed On</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($receivedDispatches as $dispatch)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ \Illuminate\Support\Carbon::parse($dispatch->distribution_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($dispatch->receiptConfirmation?->received_quantity ?? $dispatch->quantity_distributed) }} pads</td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ optional($dispatch->receiptConfirmation?->confirmation_date)->format('d M Y, H:i') ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $dispatch->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No confirmed receipts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
