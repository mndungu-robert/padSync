@extends('layouts.admin', ['active' => 'logs'])

@section('title', 'System Audit Logs - PadSync')

@section('content')
    <div>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Operational Activity</h2>
        <p class="text-xs font-medium text-gray-400 mt-0.5">Important events across M-Pesa, dispatches, shortfalls, and receipt confirmations.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Key Operations Timeline</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Details</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($timeline ?? collect()) as $event)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($event['happened_at'])->format('d M Y, H:i:s') }}
                        </td>

                        @php
                            $category = (string) ($event['category'] ?? '');
                            $categoryClass = match ($category) {
                                'M-Pesa Transaction' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'Dispatch' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'Shortfall Report' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Receipt Confirmation' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                            $status = strtolower((string) ($event['status'] ?? ''));
                            $statusClass = match (true) {
                                str_contains($status, 'completed'), str_contains($status, 'received'), str_contains($status, 'confirm'), str_contains($status, 'fully') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                str_contains($status, 'pending'), str_contains($status, 'partially'), str_contains($status, 'submitted'), str_contains($status, 'dispatched') => 'bg-amber-50 text-amber-700 border-amber-200',
                                str_contains($status, 'failed'), str_contains($status, 'cancel'), str_contains($status, 'draft') => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                        @endphp

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $categoryClass }}">
                                {{ $category }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-700 font-semibold">{{ $event['details'] }}</td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border {{ $statusClass }}">
                                {{ $event['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium">No operational activity found yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Recent M-Pesa Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Donor</th>
                        <th class="px-6 py-3.5 text-right">Amount (KES)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Receipt Ref</th>
                        <th class="px-6 py-3.5">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($mpesaTransactions ?? collect()) as $payment)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $payment->donor_name }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $payment->donor_email }}</div>
                        </td>

                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format((float) $payment->amount_kes, 2) }}</td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border
                                {{ $payment->payment_status === 'Successful' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                {{ $payment->payment_status }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-xs font-mono text-gray-500">{{ $payment->payment_reference ?? 'N/A' }}</td>

                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i:s') : 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">No M-Pesa transaction activity found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Recent Dispatches</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">School</th>
                        <th class="px-6 py-3.5 text-right">Quantity</th>
                        <th class="px-6 py-3.5">Dispatch Date</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($dispatches ?? collect()) as $dispatch)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $dispatch->school_name }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format((int) $dispatch->quantity_distributed) }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ \Carbon\Carbon::parse($dispatch->distribution_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $dispatchStatus = strtolower((string) $dispatch->status);
                                $dispatchStatusClass = match (true) {
                                    str_contains($dispatchStatus, 'received') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    str_contains($dispatchStatus, 'dispatched'), str_contains($dispatchStatus, 'pending') => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border {{ $dispatchStatusClass }}">
                                {{ $dispatch->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">No dispatch records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Recent Shortfall Reports</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">School</th>
                        <th class="px-6 py-3.5 text-right">Required</th>
                        <th class="px-6 py-3.5 text-right">Available</th>
                        <th class="px-6 py-3.5 text-right">Shortfall</th>
                        <th class="px-6 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($shortfallReports ?? collect()) as $report)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $report->school_name }}</td>
                        <td class="px-6 py-4 text-right">{{ number_format((int) $report->required_pads) }}</td>
                        <td class="px-6 py-4 text-right">{{ number_format((int) $report->available_pads) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-rose-600">{{ number_format((int) $report->shortfall) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $shortfallStatus = strtolower((string) $report->status);
                                $shortfallStatusClass = match (true) {
                                    str_contains($shortfallStatus, 'received') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    str_contains($shortfallStatus, 'dispatched'), str_contains($shortfallStatus, 'submitted') => 'bg-amber-50 text-amber-700 border-amber-200',
                                    str_contains($shortfallStatus, 'draft') => 'bg-slate-50 text-slate-700 border-slate-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border {{ $shortfallStatusClass }}">
                                {{ $report->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 font-medium">No shortfall reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Recent Receipt Confirmations</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">School</th>
                        <th class="px-6 py-3.5">Coordinator</th>
                        <th class="px-6 py-3.5 text-right">Received Qty</th>
                        <th class="px-6 py-3.5">Confirmed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($receiptConfirmations ?? collect()) as $confirmation)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800">{{ $confirmation->school_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $confirmation->coordinator_name ?? 'Coordinator' }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format((int) $confirmation->received_quantity) }}</td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ \Carbon\Carbon::parse($confirmation->confirmation_date)->format('d M Y, H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">No receipt confirmations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
