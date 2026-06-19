@php
    $active = $active ?? 'dashboard';
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coordinator Workspace - PadSync')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex flex-col bg-gray-100 text-gray-800">
    <header class="bg-[#7C2D12] text-white px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2">
            <span class="text-xl font-bold tracking-tight">PadSync</span>
            <span class="bg-orange-900 text-orange-200 text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-md uppercase border border-orange-800">Coordinator</span>
        </div>
        <div class="flex items-center space-x-2 text-sm font-medium text-orange-100">
            <span>{{ auth()->user()->name }}</span>
            <span>|</span>
            <a href="{{ route('profile.edit') }}" class="hover:text-white transition font-semibold">Profile</a>
        </div>
    </header>

    <div class="flex flex-row flex-grow min-h-0">
        @include('coordinator.partials.sidebar', ['active' => $active])

        <main class="flex-grow p-8 overflow-y-auto space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">@yield('page_title', 'Coordinator Workspace')</h2>
                <p class="text-xs font-medium text-gray-400 mt-0.5">@yield('page_subtitle', 'Manage field reporting, enrollments, and distribution readiness.')</p>
            </div>

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
