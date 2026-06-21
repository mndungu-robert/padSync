@php
    $active = $active ?? 'dashboard';
@endphp

<aside class="w-64 bg-slate-100 border-r border-gray-200 p-4 space-y-1">
    <a href="{{ route('manager.dashboard') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'dashboard' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Dashboard</span>
    </a>
    <a href="{{ route('manager.schools.index') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'schools' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Schools</span>
    </a>
    <a href="{{ route('manager.coordinators.index') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'coordinators' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Coordinators</span>
    </a>
    <a href="{{ route('manager.inventory.index') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'inventory' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Inventory</span>
    </a>
    <a href="{{ route('manager.distributions.index') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'distributions' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Distributions</span>
    </a>
    <a href="{{ route('manager.donations.index') }}"
        class="flex items-center px-4 py-3 rounded-lg {{ $active === 'donations' ? 'bg-[#0F766E] font-bold text-white' : 'hover:bg-slate-200 text-gray-700 font-medium' }} text-sm transition">
        <span>Donations</span>
    </a>
</aside>
