<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Keep Girls In School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sky-1: #e0e7ff;
            --sky-2: #eef2ff;
            --ink-1: #0f172a;
            --ink-2: #334155;
            --brand-1: #4f46e5;
            --brand-2: #4338ca;
            --accent: #db2777;
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink-1);
            background:
                radial-gradient(1200px 460px at 10% -10%, rgba(79, 70, 229, 0.24), transparent 55%),
                radial-gradient(900px 420px at 90% 0%, rgba(67, 56, 202, 0.18), transparent 55%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            min-height: 100vh;
        }

        .brand-serif {
            font-family: 'Fraunces', serif;
        }
    </style>
</head>
<body>
    @php
        $impact = $stats ?? [
            'schools_supported' => 0,
            'girls_enrolled' => 0,
            'packets_needed_monthly' => 0,
            'pads_needed_monthly' => 0,
            'trend_month_labels' => [],
            'money_received_monthly' => [],
            'pads_pledged_monthly' => [],
        ];
        $distributionSeries = collect($monthlyDistributions ?? []);
        $girlsSupportStart = (int) ($girlsSupportStart ?? 0);
        $girlsSupportCurrent = (int) ($girlsSupportCurrent ?? 0);
    @endphp

    <div class="relative overflow-hidden">
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-indigo-200/55 blur-3xl"></div>
        <div class="absolute top-40 -left-20 h-72 w-72 rounded-full bg-fuchsia-200/45 blur-3xl"></div>

        <nav class="relative z-10 px-5 py-5 md:px-10">
            <div class="mx-auto flex max-w-6xl items-center justify-between rounded-2xl border border-white/70 bg-white/75 px-4 py-3 shadow-lg backdrop-blur md:px-6">
                <h1 class="brand-serif text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{{ config('app.name') }}</h1>
                <div class="flex items-center gap-2 md:gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Coordinator Registration</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <section class="relative z-10 px-5 pb-8 pt-6 md:px-10 md:pb-14 md:pt-10">
            <div class="mx-auto grid max-w-6xl items-center gap-10 lg:grid-cols-[1.2fr_0.8fr]">
                <div>
                    <p class="mb-4 inline-flex rounded-full border border-indigo-300/70 bg-indigo-50 px-3 py-1 text-xs font-extrabold uppercase tracking-[0.14em] text-indigo-800">
                        Real Need. Real Impact.
                    </p>
                    <h2 class="brand-serif text-4xl leading-tight text-slate-900 sm:text-5xl lg:text-6xl">
                        Give One Month of Dignity,
                        <span class="text-indigo-700">Keep a Girl in Class</span>
                    </h2>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-700 md:text-lg">
                        Across {{ number_format($impact['schools_supported']) }} schools in our program, girls need reliable monthly packet support.
                        Your donation helps close that need before it becomes a crisis.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('donate.form') }}" class="rounded-xl bg-indigo-700 px-6 py-3 text-sm font-extrabold uppercase tracking-wide text-white shadow-lg shadow-indigo-900/20 transition hover:bg-indigo-800">
                            Donate Packets Now
                        </a>
                        <a href="{{ route('learn.more') }}" class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                            See How The Program Works
                        </a>
                    </div>
                </div>

                <aside class="rounded-3xl border border-white/80 bg-white/85 p-5 shadow-2xl shadow-slate-900/10 backdrop-blur sm:p-6">
                    <h3 class="brand-serif text-2xl text-slate-900">This Month's Need</h3>
                    <p class="mt-2 text-sm text-slate-600">Program-wide baseline before distribution starts.</p>

                    <div class="mt-6 grid gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Schools Supported</div>
                            <div class="mt-1 text-3xl font-black text-slate-900">{{ number_format($impact['schools_supported']) }}</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Girls Supported</div>
                            <div class="mt-1 text-3xl font-black text-slate-900">{{ number_format($impact['girls_enrolled']) }}</div>
                        </div>
                        <div class="rounded-2xl border border-fuchsia-200 bg-fuchsia-50 p-4">
                            <div class="text-xs font-bold uppercase tracking-wider text-fuchsia-700">Packets Needed Monthly</div>
                            <div class="mt-1 text-3xl font-black text-fuchsia-700">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </div>

    <section class="px-5 pb-10 md:px-10">
        <div class="mx-auto max-w-6xl rounded-3xl border border-slate-200 bg-white p-6 shadow-lg md:p-8">
            <h3 class="brand-serif text-2xl text-slate-900">Girls Support Growth</h3>
            <p class="mt-2 text-sm text-slate-600">A clear view of where the programme started and where it stands now.</p>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Girls Supported At Start</div>
                    <div class="mt-1 text-3xl font-black text-slate-900">{{ number_format($girlsSupportStart) }}</div>
                </div>
                <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-indigo-700">Girls Supported Currently</div>
                    <div class="mt-1 text-3xl font-black text-indigo-700">{{ number_format($girlsSupportCurrent) }}</div>
                </div>
            </div>

            <div class="mt-5 rounded-lg border border-gray-200 bg-white p-5">
                <h4 class="text-sm font-bold text-gray-800">Girls Supported: Start vs Current</h4>
                <div class="mt-3 h-[280px] w-full">
                    <canvas id="publicGirlsSupportGrowthChart" aria-label="Girls support growth chart" role="img"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 pb-10 md:px-10">
        <div class="mx-auto max-w-6xl rounded-3xl border border-slate-200 bg-white p-6 shadow-lg md:p-8">
            <h3 class="brand-serif text-3xl text-slate-900">Programme at a Glance</h3>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Schools Supported</div>
                    <div class="mt-1 text-3xl font-black text-slate-900">{{ number_format($impact['schools_supported']) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Girls Supported</div>
                    <div class="mt-1 text-3xl font-black text-slate-900">{{ number_format($impact['girls_enrolled']) }}</div>
                </div>
                <div class="rounded-2xl border border-fuchsia-200 bg-fuchsia-50 p-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-fuchsia-700">Packets Needed Monthly</div>
                    <div class="mt-1 text-3xl font-black text-fuchsia-700">{{ number_format($impact['packets_needed_monthly'] ?? $impact['pads_needed_monthly']) }}</div>
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
                <h4 class="text-sm font-bold text-gray-800">Pads Distributed Per Month</h4>
                <div class="mt-3 h-[280px] w-full">
                    <canvas id="publicMonthlyDistributionChart" aria-label="Pads distributed per month chart" role="img"></canvas>
                </div>
            </div>
        </div>
    </section>

    <section class="px-5 pb-16 pt-6 md:px-10">
        <div class="mx-auto max-w-6xl rounded-3xl border border-slate-200 bg-white p-6 shadow-lg md:p-8">
            <h3 class="brand-serif text-3xl text-slate-900">What Your Donation Does</h3>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-700">1. Coordinator Reports</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Monthly enrollments capture exactly how many girls need packet support.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">2. Managers Dispatch</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Program managers plan and send packet stock to schools with active monthly need.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-extrabold uppercase tracking-wider text-fuchsia-700">3. Girls Stay In Class</p>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Reliable access means fewer missed school days and stronger continuity in learning.</p>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                <a href="{{ route('donate.form') }}" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                    Make A Donation
                </a>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            if (typeof window.Chart === 'undefined') {
                return;
            }

            const distributionSeries = @json($distributionSeries);
            const girlsSupportStart = @json($girlsSupportStart);
            const girlsSupportCurrent = @json($girlsSupportCurrent);

            const growthCtx = document.getElementById('publicGirlsSupportGrowthChart');
            if (growthCtx) {
                new Chart(growthCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Start of Programme', 'Current'],
                        datasets: [{
                            label: 'Girls Supported',
                            data: [girlsSupportStart, girlsSupportCurrent],
                            backgroundColor: ['#6a2fa0', '#3a1a5c'],
                            borderRadius: 10,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });
            }

            const distributionCtx = document.getElementById('publicMonthlyDistributionChart');
            if (distributionCtx) {
                new Chart(distributionCtx, {
                    type: 'line',
                    data: {
                        labels: distributionSeries.map((entry) => entry.month),
                        datasets: [{
                            label: 'Pads Distributed',
                            data: distributionSeries.map((entry) => entry.total),
                            borderColor: '#3a1a5c',
                            backgroundColor: 'rgba(58, 26, 92, 0.12)',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });
            }
        })();
    </script>
</body>
</html>
