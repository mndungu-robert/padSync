<x-app-layout>
    @php
        $role = auth()->user()->role ?? null;

        $theme = match ($role) {
            'Admin' => [
                'accent' => '#1E3A8A',
                'accent_soft' => '#DBEAFE',
                'accent_bg' => '#EFF6FF',
                'ring' => 'rgba(30,58,138,0.28)',
                'shell_a' => 'rgba(59,130,246,0.18)',
                'shell_b' => 'rgba(30,58,138,0.14)',
            ],
            'Program Manager' => [
                'accent' => '#0F766E',
                'accent_soft' => '#CCFBF1',
                'accent_bg' => '#F0FDFA',
                'ring' => 'rgba(15,118,110,0.28)',
                'shell_a' => 'rgba(20,184,166,0.18)',
                'shell_b' => 'rgba(15,118,110,0.16)',
            ],
            'Coordinator' => [
                'accent' => '#4338CA',
                'accent_soft' => '#C7D2FE',
                'accent_bg' => '#EEF2FF',
                'ring' => 'rgba(67,56,202,0.28)',
                'shell_a' => 'rgba(99,102,241,0.18)',
                'shell_b' => 'rgba(67,56,202,0.14)',
            ],
            default => [
                'accent' => '#0F766E',
                'accent_soft' => '#CCFBF1',
                'accent_bg' => '#F0FDFA',
                'ring' => 'rgba(15,118,110,0.28)',
                'shell_a' => 'rgba(20,184,166,0.18)',
                'shell_b' => 'rgba(15,118,110,0.16)',
            ],
        };
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight tracking-tight">
                    {{ __('Account Profile') }}
                </h2>
                <p class="mt-1 text-xs font-semibold" style="color: {{ $theme['accent'] }};">
                    {{ __('Manage your account details, password security, and session access from one place.') }}
                </p>
            </div>
            <span class="hidden sm:inline-flex px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider border" style="border-color: {{ $theme['accent_soft'] }}; background: {{ $theme['accent_bg'] }}; color: {{ $theme['accent'] }};">
                {{ config('app.name') }} {{ $role ? $role : __('Portal') }}
            </span>
        </div>
    </x-slot>

    <style>
        :root {
            --profile-accent: {{ $theme['accent'] }};
            --profile-accent-soft: {{ $theme['accent_soft'] }};
            --profile-accent-bg: {{ $theme['accent_bg'] }};
            --profile-accent-ring: {{ $theme['ring'] }};
        }

        .profile-shell {
            background:
                radial-gradient(circle at 0% 0%, {{ $theme['shell_a'] }}, transparent 45%),
                radial-gradient(circle at 100% 100%, {{ $theme['shell_b'] }}, transparent 42%),
                linear-gradient(180deg, #f0fdfa 0%, #ecfeff 55%, #f8fafc 100%);
        }

        .profile-panel {
            background: #ffffff;
            border: 1px solid var(--profile-accent-soft);
            border-radius: 14px;
            box-shadow: 0 12px 28px var(--profile-accent-ring);
        }

        .profile-panel-danger {
            border-color: #fecdd3;
            box-shadow: 0 12px 28px rgba(225, 29, 72, 0.08);
        }

        .profile-input {
            border-color: #cbd5e1;
        }

        .profile-input:focus {
            border-color: var(--profile-accent);
            box-shadow: 0 0 0 2px var(--profile-accent-soft);
        }

        .profile-primary-btn {
            background: var(--profile-accent);
            color: #fff;
        }

        .profile-primary-btn:hover {
            filter: brightness(0.94);
        }

        .profile-accent-text {
            color: var(--profile-accent);
        }
    </style>

    <div class="py-10 profile-shell">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="p-5 sm:p-8 profile-panel">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-5 sm:p-8 profile-panel">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="p-5 sm:p-8 profile-panel profile-panel-danger">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <div class="p-5 sm:p-8 profile-panel">
                    <div class="max-w-xl">
                        <h3 class="text-lg font-semibold text-slate-900">{{ __('Log Out') }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ __('End your current session from this device.') }}</p>

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2.5 rounded-md text-xs font-bold uppercase tracking-wider transition profile-primary-btn"
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
