<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight tracking-tight">
                    {{ __('Account Profile') }}
                </h2>
                <p class="mt-1 text-xs font-medium text-gray-400">
                    {{ __('Manage your account details, password security, and session access.') }}
                </p>
            </div>
            <span class="hidden sm:inline-flex px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-indigo-200 bg-indigo-50 text-indigo-700">
                {{ __('PadSync Portal') }}
            </span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="p-5 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-5 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="p-5 sm:p-8 bg-white border border-rose-200 shadow-sm rounded-xl">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <div class="p-5 sm:p-8 bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Log Out') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ __('End your current session from this device.') }}</p>

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2.5 rounded-md bg-indigo-600 text-white text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 transition"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
