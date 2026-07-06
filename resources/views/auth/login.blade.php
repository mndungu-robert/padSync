<x-guest-layout>
    <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-md rounded-xl border border-gray-100 text-center mx-auto my-auto">
        <div class="mb-6 text-left">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1E3A8A] hover:text-[#152A66] transition duration-150 ease-in-out" aria-label="Go back to home page">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back</span>
            </a>
        </div>

        
        <!-- Branding Headers -->
        <h1 class="text-3xl font-extrabold text-[#1E3A8A] tracking-tight mb-1">{{ config('app.name') }}</h1>
        <p class="text-xs font-semibold text-gray-400 mb-8">Macheo Programme · Strathmore University</p>

        <!-- Status Alerts -->
        @if (session('status'))
            <div class="mb-4 font-medium text-xs text-green-600 bg-green-50 p-3 rounded-md text-left">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="text-left">
            @csrf

            <!-- Username or Email Input -->
            <div>
                <label for="login" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Username or Email <span class="text-red-500">*</span></label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus placeholder="Enter username or email"
                    class="block mt-1 w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] transition duration-150 ease-in-out text-sm">
                @error('login')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="mt-5">
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Password <span class="text-red-500">*</span></label>
                <div class="relative mt-1">
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                        class="block w-full px-4 py-2.5 pr-11 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] transition duration-150 ease-in-out text-sm">
                    <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 z-10 text-gray-500 hover:text-[#1E3A8A]" aria-label="Show password" title="Show password">
                        <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.293-3.95M9.88 9.88a3 3 0 104.24 4.24" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1A9.963 9.963 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.043 5.208M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sign In Action Button -->
            <div class="mt-6">
                <button type="submit" class="w-28 py-2.5 px-4 rounded-md shadow-md text-sm font-bold text-white bg-[#1E3A8A] hover:bg-[#152A66] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1E3A8A] transition duration-150 ease-in-out">
                    Sign In
                </button>
            </div>
        </form>

        <!-- Registration Navigation Redirect -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-xs text-gray-500">
            School Coordinator? <a href="{{ route('register') }}" class="font-bold text-[#1E3A8A] hover:underline">Register here</a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        if (passwordInput && togglePassword) {
            togglePassword.addEventListener('click', function () {
                const showPassword = passwordInput.type === 'password';
                passwordInput.type = showPassword ? 'text' : 'password';

                // Keep icon state intuitive: slashed eye when hidden, open eye when visible.
                eyeOpen.classList.toggle('hidden', !showPassword);
                eyeClosed.classList.toggle('hidden', showPassword);

                togglePassword.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
                togglePassword.setAttribute('title', showPassword ? 'Hide password' : 'Show password');
            });
        }
    </script>
</x-guest-layout>

        <div class="mb-6 text-left">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1E3A8A] hover:text-[#152A66] transition duration-150 ease-in-out" aria-label="Go back to home page">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Back</span>
            </a>
        </div>
