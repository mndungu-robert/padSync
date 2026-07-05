@php
    $active = $active ?? 'dashboard';
@endphp

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Workspace - ' . config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full flex flex-col bg-gray-100 text-gray-800">
    <header class="bg-[#1E3A8A] text-white px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2">
            <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
            <span class="bg-blue-900 text-blue-200 text-[10px] font-bold tracking-wider px-2 py-0.5 rounded-md uppercase border border-blue-800">Admin</span>
        </div>
        <div class="flex items-center space-x-2 text-sm font-medium text-slate-200">
            <span>{{ auth()->user()->name }}</span>
            <span>|</span>
            <a href="{{ route('profile.edit') }}" class="hover:text-white transition font-semibold">Profile</a>
        </div>
    </header>

    <div class="flex flex-row flex-grow min-h-0">
        @include('admin.partials.sidebar', ['active' => $active])

        <main class="flex-grow p-8 overflow-y-auto space-y-6">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>
