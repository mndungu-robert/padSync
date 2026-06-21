@php
    $active = $active ?? 'dashboard';
@endphp

<aside class="w-64 bg-slate-100 border-r border-gray-200 p-4 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'dashboard' ? 'bg-[#1E3A8A] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'users' ? 'bg-[#1E3A8A] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Manage Users</span>
    </a>
    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
        <span>Audit Logs</span>
    </a>
    <a href="#" class="flex items-center px-4 py-3 rounded-lg hover:bg-slate-200 text-gray-700 font-medium text-sm transition">
        <span>Settings</span>
    </a>
</aside>