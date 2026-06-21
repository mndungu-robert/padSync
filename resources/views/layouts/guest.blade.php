<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Main flex column container that controls full-viewport layout centering -->
        <div class="min-h-screen flex flex-col justify-between items-center bg-[#F3F4F6] py-12 px-4">
            
            <!-- Spacer to push content down cleanly -->
            <div></div>

            <!-- This is where your login card renders -->
            <div class="w-full flex justify-center items-center">
                {{ $slot }}
            </div>

            <!-- Sticky, centered page footer -->
            <footer class="text-[11px] text-gray-400 text-center font-medium tracking-wide mt-8">
                {{ config('app.name') }} · Macheo Programme · ICS3 Group E · Strathmore University 2026
            </footer>
        </div>
    </body>
</html>
