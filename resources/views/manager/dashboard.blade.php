@extends('layouts.manager')

@section('title', 'Program Manager Dashboard - ' . config('app.name'))
@section('page_title', 'Program Manager Dashboard')
@section('page_subtitle', 'High-level operational visibility for schools, coordinators, and inventory.')

@section('content')
    @php
        $dashboardMetrics = array_merge([
            'available_stock' => 0,
            'money_received' => 0,
            'schools_count' => 0,
            'active_shortfalls' => 0,
            'pending_profiles' => 0,
            'required_pads' => 0,
            'covered_pads' => 0,
            'remaining_pads' => 0,
            'coverage_percent' => 0,
        ], $metrics ?? []);
        $dashboardSchoolStats = collect($schoolStats ?? []);
        $dashboardInventory = array_merge([
            'available' => 0,
            'allocated' => 0,
            'dispatched_this_month' => 0,
        ], $inventoryStats ?? []);
        $criticalNeedRows = collect($criticalNeeds ?? []);
        $hasActiveEmergency = ((int) $dashboardMetrics['active_shortfalls']) > 0;
    @endphp

    @if($hasActiveEmergency)
        <div class="mb-5 rounded-xl border border-rose-300 bg-gradient-to-r from-rose-100 via-rose-50 to-orange-50 px-5 py-3 shadow-sm">
            <p class="text-xs font-extrabold uppercase tracking-wider text-rose-700">Emergency Attention Required</p>
            <p class="text-sm font-semibold text-rose-900 mt-1">
                {{ number_format((int) $dashboardMetrics['active_shortfalls']) }} active shortfall request(s) require action.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5">
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available Stock</div>
            <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($dashboardMetrics['available_stock']) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Money Received (KES)</div>
            <div class="text-2xl font-bold text-emerald-700 mt-2">{{ number_format((float) $dashboardMetrics['money_received'], 2) }}</div>
        </div>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Registered Schools</div>
            <div class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($dashboardMetrics['schools_count']) }}</div>
        </div>
        <a href="{{ route('manager.distributions.index') }}" class="block p-5 rounded-xl shadow-sm border transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-400 {{ $hasActiveEmergency ? 'bg-rose-50 border-rose-300 ring-1 ring-rose-200 hover:bg-rose-100/60' : 'bg-white border-gray-200 hover:bg-gray-50' }}" aria-label="Open dispatch allocation screen">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider {{ $hasActiveEmergency ? 'text-rose-600' : 'text-gray-400' }}">Active Shortfalls</div>
                    <div class="text-2xl font-black mt-2 {{ $hasActiveEmergency ? 'text-rose-700' : 'text-slate-800' }}">{{ number_format($dashboardMetrics['active_shortfalls']) }}</div>
                    @if($hasActiveEmergency)
                        <div class="text-[10px] font-bold text-rose-700 mt-1">Emergency queue is open</div>
                    @else
                        <div class="text-[10px] font-semibold text-gray-500 mt-1">Open allocation screen</div>
                    @endif
                </div>
            </div>
        </a>
        <div class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Coordinators</div>
            <div class="text-2xl font-bold text-teal-700 mt-2">{{ number_format($dashboardMetrics['pending_profiles']) }}</div>
        </div>
    </div>

    <div class="mt-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-gray-800">Current Month Coverage Snapshot</h3>
            <p class="text-xs font-semibold text-gray-500">
                {{ number_format((int) $dashboardMetrics['coverage_percent']) }}% covered network-wide
            </p>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                <p class="text-[10px] uppercase tracking-wide text-slate-500 font-bold">Required Pads</p>
                <p class="text-xl font-black text-slate-800 mt-1">{{ number_format((int) $dashboardMetrics['required_pads']) }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                <p class="text-[10px] uppercase tracking-wide text-emerald-700 font-bold">Covered So Far</p>
                <p class="text-xl font-black text-emerald-700 mt-1">{{ number_format((int) $dashboardMetrics['covered_pads']) }}</p>
            </div>
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-3">
                <p class="text-[10px] uppercase tracking-wide text-rose-700 font-bold">Still Needed</p>
                <p class="text-xl font-black text-rose-700 mt-1">{{ number_format((int) $dashboardMetrics['remaining_pads']) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        <div class="xl:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-800">School Shortfall Overview</h3>
            <div class="mt-3 h-[280px] w-full">
                <canvas id="managerSchoolShortfallChart" aria-label="School shortfall overview chart" role="img"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-800">Inventory Status</h3>
            <div class="mt-3 h-[220px] w-full">
                <canvas id="managerInventoryStatusChart" aria-label="Inventory status chart" role="img"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Top Critical Shortfall Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">School</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Required</th>
                        <th class="px-6 py-3">Available</th>
                        <th class="px-6 py-3">Shortfall</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($criticalNeedRows as $row)
                        <tr class="transition hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $row->school->school_name ?? 'Unknown school' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $row->status === 'Submitted' ? 'bg-slate-100 text-slate-700 border-slate-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ number_format((int) $row->required_pads) }}</td>
                            <td class="px-6 py-4">{{ number_format((int) $row->available_pads) }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ number_format((int) $row->shortfall) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No active shortfall emergencies found (Submitted or Dispatched with shortfall above zero).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            if (typeof window.Chart === 'undefined') {
                return;
            }

            const dashboardSchoolStats = @json($dashboardSchoolStats);
            const dashboardInventory = @json($dashboardInventory);

            const schoolCtx = document.getElementById('managerSchoolShortfallChart');
            if (schoolCtx) {
                new Chart(schoolCtx, {
                    type: 'bar',
                    data: {
                        labels: dashboardSchoolStats.map((entry) => entry.name),
                        datasets: [
                            {
                                label: 'Covered So Far',
                                data: dashboardSchoolStats.map((entry) => entry.received),
                                backgroundColor: '#1a5c3a',
                            },
                            {
                                label: 'Shortfall',
                                data: dashboardSchoolStats.map((entry) => entry.shortfall),
                                backgroundColor: '#c0392b',
                            },
                        ],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                    },
                });
            }

            const inventoryCtx = document.getElementById('managerInventoryStatusChart');
            if (inventoryCtx) {
                new Chart(inventoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available (Now)', 'Allocated (Now)', 'Dispatched (This Month)'],
                        datasets: [{
                            data: [
                                dashboardInventory.available ?? 0,
                                dashboardInventory.allocated ?? 0,
                                dashboardInventory.dispatched_this_month ?? 0,
                            ],
                            backgroundColor: ['#1a5c3a', '#f39c12', '#c0392b'],
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                        },
                    },
                });
            }
        })();
    </script>
@endpush
