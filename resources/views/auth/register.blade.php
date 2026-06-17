<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F3F4F6]">
        <!-- Main Form Card Container Enclave -->
        <div class="w-full sm:max-w-md mt-6 px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg border border-gray-100 text-center">
            
            <!-- Branding Headers -->
            <h1 class="text-3xl font-extrabold text-[#1E3A8A] tracking-tight mb-1">PadSync</h1>
            <p class="text-sm font-medium text-gray-400 mb-6">Coordinator Registration Portal</p>

            <form method="POST" action="{{ route('register') }}" class="text-left">
                @csrf

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe"
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] text-sm">
                    @error('name') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Username -->
                <div class="mt-4">
                    <label for="username" class="block text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required placeholder="johndoe12"
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] text-sm">
                    @error('username') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="coordinator@example.com"
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] text-sm">
                    @error('email') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Dynamic School Selection Dropdown Menu Component -->
                <div class="mt-4">
                    <label for="school_id" class="block text-sm font-semibold text-gray-700">Assigned School Site <span class="text-red-500">*</span></label>
                    <select id="school_id" name="school_id" required 
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] bg-white text-sm">
                        <option value="" disabled selected>-- Select your school site --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->school_id }}" {{ old('school_id') == $school->school_id ? 'selected' : '' }}>
                                {{ $school->school_name }} ({{ $school->school_location }})
                            </option>
                        @endforeach
                    </select>
                    @error('school_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password <span class="text-red-500">*</span></label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="********"
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] text-sm">
                    @error('password') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="********"
                        class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-[#1E3A8A] focus:border-[#1E3A8A] text-sm">
                </div>

                <!-- Action Button Container -->
                <div class="mt-6">
                    <button type="submit" class="w-32 py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-[#1E3A8A] hover:bg-[#152A66] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1E3A8A] transition duration-150 ease-in-out transform active:scale-95">
                        Register
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500">
                Already registered? <a href="{{ route('login') }}" class="font-bold text-[#1E3A8A] hover:underline">Sign In instead</a>
            </div>
        </div>

        <footer class="mt-8 text-xs text-gray-400 text-center font-medium tracking-wide">
            PadSync · Macheo Programme · ICS3 Group E · Strathmore University 2026
        </footer>
    </div>
</x-guest-layout>
