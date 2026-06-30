@extends('layouts.admin', ['active' => 'logs'])

@section('title', 'System Audit Logs - PadSync')

@section('content')
    <div>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Audit Logs</h2>
        <p class="text-xs font-medium text-gray-400 mt-0.5">Recent system activity and user actions.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">System Activity Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Log ID</th>
                        <th class="px-6 py-3.5">Date & Time</th>
                        <th class="px-6 py-3.5">User ID</th>
                        <th class="px-6 py-3.5">Role</th>
                        <th class="px-6 py-3.5">Action</th>
                        <th class="px-6 py-3.5 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 text-xs font-mono text-gray-400">#{{ $log->log_id }}</td>

                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                        </td>

                        <td class="px-6 py-4 font-bold text-gray-800">{{ $log->user_id }}</td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border
                                {{ $log->user_role === 'Admin' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-teal-50 text-teal-700 border-teal-200' }}">
                                {{ $log->user_role }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-700 font-semibold">{{ $log->action_performed }}</td>

                        <td class="px-6 py-4 text-right font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Recent Money Donation Payment Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Donation</th>
                        <th class="px-6 py-3.5">Donor</th>
                        <th class="px-6 py-3.5 text-right">Amount (KES)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Receipt Ref</th>
                        <th class="px-6 py-3.5">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse(($paymentActivity ?? collect()) as $payment)
                    <tr class="hover:bg-gray-50/40 transition">
                        <td class="px-6 py-4 text-xs font-mono text-gray-500">#{{ $payment->donation_id }}</td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">{{ $payment->donor_name }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $payment->donor_email }}</div>
                        </td>

                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format((float) $payment->amount_kes, 2) }}</td>

                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide border
                                {{ $payment->payment_status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($payment->payment_status === 'Pending' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
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
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-medium">No money donation payment activity found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
