<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - PadSync</title>
    <link rel="preconnect" href="https://bunny.net">
    <link href="https://bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex flex-col bg-gray-100 text-gray-800">
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

    <div class="flex flex-row flex-grow min-h-0">
        <aside class="w-64 bg-slate-100 border-r border-gray-200 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg bg-[#1E3A8A] font-bold text-white transition">
                <span class="text-sm">Manage Users</span>
            </a>
        </aside>

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

            @if ($errors->any())
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manage Users</h2>
                <p class="text-xs font-medium text-gray-400 mt-0.5">View all users, approve coordinators, and add managers.</p>
            </div>

            <div class="flex justify-end">
                <button type="button" id="open-add-user-modal" class="rounded-md bg-[#1E3A8A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#173071] transition">
                    Add Users
                </button>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-sm text-gray-800">All System Users</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">School</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-600">
                                @forelse($allUsers as $systemUser)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-800">{{ $systemUser->name }}</div>
                                            <div class="font-mono text-xs text-gray-400">{{ $systemUser->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">{{ $systemUser->role }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                                                {{ $systemUser->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $systemUser->status === 'Pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $systemUser->status === 'Rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                            ">
                                                {{ $systemUser->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500">{{ $systemUser->school?->school_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if ($systemUser->role === 'Coordinator')
                                                <form method="POST" action="{{ route('admin.users.coordinator-status', $systemUser) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="text-xs border-gray-300 rounded-md px-2 py-1">
                                                        <option value="Approved" @selected($systemUser->status === 'Approved')>Approve</option>
                                                        <option value="Rejected" @selected($systemUser->status === 'Rejected')>Reject</option>
                                                    </select>
                                                    <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Save</button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 font-medium">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
            </div>

            <div id="add-user-modal" class="fixed inset-0 z-50 hidden">
                <div class="absolute inset-0 bg-black/40" id="close-add-user-modal-overlay"></div>
                <div class="relative z-10 mx-auto mt-16 w-full max-w-xl px-4">
                    <div class="rounded-xl bg-white border border-gray-200 shadow-xl p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">Add User</h3>
                                <p class="text-xs text-gray-400 mt-1">Create a Program Manager account.</p>
                            </div>
                            <button type="button" id="close-add-user-modal" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('admin.users.program-managers.store') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-500">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-500">Username</label>
                                <input type="text" name="username" value="{{ old('username') }}" class="mt-1 w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-500">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-500">Password</label>
                                <input type="password" name="password" class="mt-1 w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold uppercase tracking-wider text-gray-500">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="mt-1 w-full text-sm border-gray-300 rounded-md" required>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" id="cancel-add-user-modal" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                                <button type="submit" class="rounded-md bg-[#1E3A8A] px-3 py-2 text-sm font-semibold text-white hover:bg-[#173071] transition">Create Manager</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                const addUserModal = document.getElementById('add-user-modal');
                const openAddUserModal = document.getElementById('open-add-user-modal');
                const closeAddUserModal = document.getElementById('close-add-user-modal');
                const closeAddUserOverlay = document.getElementById('close-add-user-modal-overlay');
                const cancelAddUserModal = document.getElementById('cancel-add-user-modal');

                function showAddUserModal() {
                    addUserModal.classList.remove('hidden');
                }

                function hideAddUserModal() {
                    addUserModal.classList.add('hidden');
                }

                if (openAddUserModal) {
                    openAddUserModal.addEventListener('click', showAddUserModal);
                }

                if (closeAddUserModal) {
                    closeAddUserModal.addEventListener('click', hideAddUserModal);
                }

                if (closeAddUserOverlay) {
                    closeAddUserOverlay.addEventListener('click', hideAddUserModal);
                }

                if (cancelAddUserModal) {
                    cancelAddUserModal.addEventListener('click', hideAddUserModal);
                }

                @if ($errors->any())
                    showAddUserModal();
                @endif
            </script>
        </main>
    </div>
</body>
</html>
