@extends('layouts.admin', ['active' => 'logs'])

@section('title', 'System Audit Logs - PadSync')
@section('page_title', 'System Audit Logs')
@section('page_subtitle', 'Complete unalterable historical trace log of all core database updates and security operations.')

@section('content')
    <!-- Audit Trail Table Card Container Box -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-sm text-gray-800">Unalterable Security Audit Stream</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3.5">Log ID</th>
                        <th class="px-6 py-3.5">Timestamp</th>
                        <th class="px-6 py-3.5">User ID Context</th>
                        <th class="px-6 py-3.5">Role Level</th>
                        <th class="px-6 py-3.5">Action Performed</th>
                        <th class="px-6 py-3.5 text-right">Originating IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/40 transition">
                        <!-- Log ID Tracking Token -->
                        <td class="px-6 py-4 text-xs font-mono text-gray-400">#{{ $log->log_id }}</td>
                        
                        <!-- Formatted Timestamp -->
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                        </td>
                        
                        <!-- Acting User Record Reference -->
                        <td class="px-6 py-4 font-bold text-gray-800">User ID: {{ $log->user_id }}</td>
                        
                        <!-- Role Level Context -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border
                                {{ $log->user_role === 'Admin' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-teal-50 text-teal-700 border-teal-200' }}">
                                {{ $log->user_role }}
                            </span>
                        </td>
                        
                        <!-- Executed Operation Description String -->
                        <td class="px-6 py-4 text-sm text-slate-700 font-semibold">{{ $log->action_performed }}</td>
                        
                        <!-- Contextual IP Tracking Node Address -->
                        <td class="px-6 py-4 text-right font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">No internal security operations or historical ledger entries are present in database traces.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginator Links Component Row Container Box Footer -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
