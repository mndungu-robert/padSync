@php
    $role = auth()->user()->role ?? null;

    $theme = match ($role) {
        'Admin' => [
            'nav' => 'bg-[#1E3A8A] border-b border-blue-900',
            'brand' => 'text-white',
            'badge' => 'bg-blue-900 text-blue-200 border border-blue-800',
            'user' => 'text-blue-100',
            'trigger' => 'text-blue-100 hover:text-white bg-blue-900/40',
            'menu' => 'bg-white',
        ],
        'Program Manager' => [
            'nav' => 'bg-[#0F766E] border-b border-teal-900',
            'brand' => 'text-white',
            'badge' => 'bg-teal-900 text-teal-200 border border-teal-800',
            'user' => 'text-teal-100',
            'trigger' => 'text-teal-100 hover:text-white bg-teal-900/40',
            'menu' => 'bg-white',
        ],
        'Coordinator' => [
            'nav' => 'bg-indigo-700 border-b border-indigo-900',
            'brand' => 'text-white',
            'badge' => 'bg-indigo-900 text-indigo-200 border border-indigo-800',
            'user' => 'text-indigo-100',
            'trigger' => 'text-indigo-100 hover:text-white bg-indigo-900/40',
            'menu' => 'bg-white',
        ],
        default => [
            'nav' => 'bg-white border-b border-gray-100',
            'brand' => 'text-indigo-700',
            'badge' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'user' => 'text-gray-700',
            'trigger' => 'text-gray-500 hover:text-gray-700 bg-white',
            'menu' => 'bg-white',
            'link' => 'text-gray-700 hover:text-indigo-700 hover:bg-indigo-50',
            'link_active' => 'text-indigo-700 bg-indigo-50 border border-indigo-200',
        ],
    };

    if ($role === 'Admin') {
        $theme['link'] = 'text-blue-100 hover:text-white hover:bg-blue-800/70';
        $theme['link_active'] = 'text-white bg-blue-900/70 border border-blue-700';
    }

    if ($role === 'Program Manager') {
        $theme['link'] = 'text-teal-100 hover:text-white hover:bg-teal-800/70';
        $theme['link_active'] = 'text-white bg-teal-900/70 border border-teal-700';
    }

    if ($role === 'Coordinator') {
        $theme['link'] = 'text-indigo-100 hover:text-white hover:bg-indigo-800/70';
        $theme['link_active'] = 'text-white bg-indigo-900/70 border border-indigo-700';
    }
@endphp

<nav x-data="{ open: false }" class="{{ $theme['nav'] }}">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight {{ $theme['brand'] }}">{{ config('app.name') }}</span>
                        @if($role)
                            <span class="hidden lg:inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $theme['badge'] }}">{{ $role }}</span>
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md text-sm font-semibold transition {{ request()->routeIs('dashboard') ? $theme['link_active'] : $theme['link'] }}">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-md {{ $theme['trigger'] }} focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="{{ $theme['menu'] }}">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white/90 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block w-full px-4 py-2 text-base font-semibold transition {{ request()->routeIs('dashboard') ? 'text-slate-900 bg-slate-100' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20 bg-white/95 backdrop-blur-sm">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
