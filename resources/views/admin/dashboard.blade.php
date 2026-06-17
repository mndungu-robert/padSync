<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PadSync</title>
    <link rel="preconnect" href="https://bunny.net">
    <link href="https://bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex flex-col bg-gray-100 text-gray-800">

    <!-- Top Navigation Bar -->
    <header class="bg-[#1E3A8A] text-white px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2">
            <span class="text-xl font-bold tracking-tight">PadSync</span>
            <span class="bg-blue-900 text-blue-200 text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-md uppercase border border-blue-800">Admin</span>
        </div>
        <div class="flex items-center space-x-2 text-sm font-medium text-slate-200">
            <span>{{ auth()->user()->name }}</span>
            <span>|</span>
            <a href="{{ route('profile.edit') }}" class="hover:text-white transition font-semibold">Profile</a>
        </div>
    </header>

    <!-- Workspace Main Frame Layout -->
    <div class="flex flex-row flex-grow min-h-0">
        
        <!-- Left Sidebar Sidebar Component Drawer -->
        <aside class="w-64 bg-slate-100 border-r border-gray-200 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg bg-[#1E3A8A] font-bold text-white transition">
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
                <span>Manage Users</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
                <span>Audit Logs</span>
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
                <span>Settings</span>
            </a>
        </aside>

        <!-- Right View Workspace Panel Frame -->
        <main class="flex-grow p-8 overflow-y-auto space-y-6">

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
            
            <!-- Headers Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Admin Dashboard</h2>
                <p class="text-xs font-medium text-gray-400 mt-0.5">System overview — all roles & activity</p>
            </div>

            <!-- Operational Metric Grid Segment -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat Card: Program Managers -->
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Program Managers</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['program_managers'] }}</div>
                </div>
                
                <!-- Stat Card: School Coordinators -->
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">School Coordinators</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['school_coordinators'] }}</div>
                </div>

                <!-- Stat Card: Schools Registered -->
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Schools Registered</div>
                    <div class="text-3xl font-bold text-gray-800 mt-2">{{ $metrics['schools_registered'] }}</div>
                </div>

                <!-- Stat Card: Pending Approvals Alert Matrix -->
                <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Approvals</div>
                    <div class="text-3xl font-bold text-rose-600 mt-2">{{ $metrics['pending_approvals'] }}</div>
                    <div class="text-[10px] text-gray-400 font-medium mt-1">awaiting review</div>
                </div>
            </div>

            <!-- Recent User Registrations Component Layout -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
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

            <!-- Recent System Activity Audit Logs Data Table -->
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

        </main>
    </div>
</body>
</html>
