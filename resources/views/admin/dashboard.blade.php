@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Admin Dashboard - ' . config('app.name'))

@section('content')
    @php
        $dashboardCoverageStats = collect($coverageStats ?? []);
        $dashboardRiskSnapshot = array_merge([
            'on_track' => 0,
            'at_risk' => 0,
            'critical' => 0,
        ], $riskSnapshot ?? []);
        $dashboardDispatchMonths = collect($dispatchMonths ?? []);
        $dashboardDispatchSchools = collect($schoolDispatchSchools ?? []);
        $dashboardDispatchMonthly = $schoolDispatchMonthly ?? [];
        $defaultDispatchMonth = $dashboardDispatchMonths->isNotEmpty()
            ? $dashboardDispatchMonths->last()['key']
            : null;
    @endphp

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Admin Dashboard</h2>
        <p class="text-xs font-medium text-gray-400 mt-0.5">System overview - all roles and activity</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Program Managers</div>
            <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['program_managers'] }}</div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">School Coordinators</div>
            <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['school_coordinators'] }}</div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Schools Registered</div>
            <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['schools_registered'] }}</div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Approvals</div>
            <div class="text-3xl font-bold text-rose-600 mt-2">{{ $metrics['pending_approvals'] }}</div>
            <div class="text-[10px] text-gray-400 font-medium mt-1">awaiting review</div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Money Received (KES)</div>
            <div class="text-3xl font-bold text-emerald-700 mt-2">{{ number_format((float) $metrics['money_received'], 2) }}</div>
        </div>

    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-800">Coverage Gap Trend</h3>
        <p class="text-xs text-gray-500 mt-1">Monthly required pads vs covered pads vs remaining gap.</p>
        <div class="mt-3 h-[280px] w-full">
            <canvas id="adminCoverageGapChart" aria-label="Coverage gap trend chart" role="img"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-sm text-gray-800">Recent User Registrations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Registered</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600">
                        @forelse($recentUsers as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->role }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-xs">{{ $user->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                                    {{ $user->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $user->status === 'Pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $user->status === 'Rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $user->status === 'Approved' ? 'Active' : $user->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No recent user registrations logged.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Pads Sent Per School</h3>
                    <p class="text-xs text-gray-500 mt-1">Choose a month to compare dispatch volume by school.</p>
                </div>
                <div>
                    <label for="adminDispatchMonthSelect" class="sr-only">Select month</label>
                    <select id="adminDispatchMonthSelect" class="rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @foreach($dashboardDispatchMonths as $month)
                            <option value="{{ $month['key'] }}" {{ $defaultDispatchMonth === $month['key'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3 h-[220px] w-full">
                <canvas id="adminSchoolDispatchChart" aria-label="Pads sent by school chart" role="img"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Recent Audit Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Action</th>
                        <th class="px-6 py-3">Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">Admin1</td>
                        <td class="px-6 py-4">{{ $log->action_performed }}</td>
                        <td class="px-6 py-4 text-xs text-gray-400 font-mono">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 font-medium">No system activity events recorded.</td>
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

            const dashboardCoverageStats = @json($dashboardCoverageStats);
            const dashboardRiskSnapshot = @json($dashboardRiskSnapshot);
            const dashboardDispatchSchools = @json($dashboardDispatchSchools);
            const dashboardDispatchMonthly = @json($dashboardDispatchMonthly);
            const defaultDispatchMonth = @json($defaultDispatchMonth);

            const coverageCtx = document.getElementById('adminCoverageGapChart');
            if (coverageCtx) {
                new Chart(coverageCtx, {
                    type: 'bar',
                    data: {
                        labels: dashboardCoverageStats.map((entry) => entry.month),
                        datasets: [
                            {
                                label: 'Required',
                                data: dashboardCoverageStats.map((entry) => entry.required),
                                backgroundColor: '#334155',
                                borderRadius: 6,
                            },
                            {
                                label: 'Covered',
                                data: dashboardCoverageStats.map((entry) => entry.covered),
                                backgroundColor: '#1a5c3a',
                                borderRadius: 6,
                            },
                            {
                                label: 'Gap',
                                data: dashboardCoverageStats.map((entry) => entry.gap),
                                backgroundColor: '#c0392b',
                                borderRadius: 6,
                            },
                        ],
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

            const dispatchCtx = document.getElementById('adminSchoolDispatchChart');
            const dispatchMonthSelect = document.getElementById('adminDispatchMonthSelect');
            if (dispatchCtx && dispatchMonthSelect) {
                const buildMonthData = (monthKey) => dashboardDispatchMonthly[monthKey] ?? dashboardDispatchSchools.map(() => 0);

                const dispatchChart = new Chart(dispatchCtx, {
                    type: 'bar',
                    data: {
                        labels: dashboardDispatchSchools,
                        datasets: [{
                            label: 'Pads Sent',
                            data: buildMonthData(defaultDispatchMonth),
                            backgroundColor: '#1E3A8A',
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });

                dispatchMonthSelect.addEventListener('change', (event) => {
                    const nextMonthKey = event.target.value;
                    dispatchChart.data.datasets[0].data = buildMonthData(nextMonthKey);
                    dispatchChart.update();
                });
            }
        })();
    </script>
@endpush
