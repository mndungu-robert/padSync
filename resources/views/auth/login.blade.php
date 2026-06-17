<x-guest-layout>
    <!-- Card Inner Base Container Layout -->
    <div class="w-full sm:max-w-md px-8 py-10 bg-slate-900/90 border border-slate-800/80 backdrop-blur-xl rounded-3xl shadow-2xl text-center">
        
        <!-- Headers -->
        <h1 class="text-3xl font-black text-white tracking-tight mb-1">Welcome Back</h1>
        <p class="text-xs font-bold text-slate-400 tracking-wide mb-8">Sign in to your PadSync operational portal</p>

        <!-- Session Flash Notification Alerts -->
        @if (session('status'))
            <div class="mb-5 font-semibold text-xs text-amber-400 bg-amber-500/10 border border-amber-500/20 p-3.5 rounded-xl text-left">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="text-left space-y-5">
            @csrf

            <!-- Unified Credential Field Input Box Component -->
            <div>
                <label for="login" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Username or Email <span class="text-amber-400">*</span></label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus placeholder="admin1 or user@padsync.com"
                    class="block w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150 ease-in-out text-sm">
                @error('login')
                    <p class="text-xs text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Password <span class="text-amber-400">*</span></label>
                    @if (Route::has('password.request'))
                        <a class="text-xs text-slate-500 hover:text-blue-400 font-medium transition" href="{{ route('password.request') }}">
                            Forgot?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                    class="block w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition duration-150 ease-in-out text-sm">
                @error('password')
                    <p class="text-xs text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Interactive Execution Button Link -->
            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl shadow-lg shadow-blue-600/10 text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform active:scale-[0.98]">
                    Sign In to Portal
                </button>
            </div>
        </form>

        <!-- Dynamic Shift Registration Redirect Router Link -->
        <div class="mt-8 pt-6 border-t border-slate-800/60 text-xs text-slate-400 font-medium">
            School Site Coordinator? <a href="{{ route('register') }}" class="font-bold text-amber-400 hover:text-amber-300 hover:underline transition ml-1">Register here &rarr;</a>
        </div>
    </div>
</x-guest-layout>
