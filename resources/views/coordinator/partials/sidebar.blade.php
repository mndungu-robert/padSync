@php
    $active = $active ?? 'dashboard';
@endphp

<aside class="w-64 bg-slate-100 border-r border-gray-200 p-4 space-y-1">
    <a href="{{ route('coordinator.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'dashboard' ? 'bg-indigo-700 font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('coordinator.enrollments.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'enrollments' ? 'bg-indigo-700 font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Enrollments</span>
    </a>
    <a href="{{ route('coordinator.shortfalls.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'shortfalls' ? 'bg-indigo-700 font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Shortfall Reports</span>
    </a>
    <a href="{{ route('coordinator.distributions.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ $active === 'distributions' ? 'bg-indigo-700 font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Distributions</span>
    </a>
</aside>
