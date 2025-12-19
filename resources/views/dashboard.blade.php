<x-app-layout>
    {{-- Kita kosongkan header bawaan Jetstream --}}
    <x-slot name="header"></x-slot>

    <div class="min-h-screen bg-gray-100">
        <div class="flex">

            {{-- ========== SIDEBAR ========== --}}
            <aside class="w-64 bg-white shadow-md min-h-screen">
                {{-- Logo --}}
                <div class="h-16 flex items-center px-6 border-b">
                    <span class="text-2xl font-extrabold text-blue-600">E-Surat.</span>
                </div>

                {{-- Menu --}}
                <nav class="mt-4">
                    {{-- Menu aktif: Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-6 py-3 text-sm font-medium bg-blue-50 text-blue-600 border-r-4 border-blue-500">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
                            </svg>
                        </span>
                        Dashboard
                    </a>

                    {{-- Nanti ganti "#" dengan route masing-masing --}}
                    <a href="#"
                       class="flex items-center px-6 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                            </svg>
                        </span>
                        Akun User
                    </a>

                    <a href="#"
                       class="flex items-center px-6 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4.5 4.5h11.25L19.5 8.25V19.5H4.5z" />
                            </svg>
                        </span>
                        Template Surat
                    </a>

                    <a href="#"
                       class="flex items-center px-6 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 7.5l9 6 9-6M4.5 5.25h15v13.5h-15z" />
                            </svg>
                        </span>
                        Surat Masuk
                    </a>

                    <a href="#"
                       class="flex items-center px-6 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4.5 12h15M12 4.5l7.5 7.5L12 19.5" />
                            </svg>
                        </span>
                        Surat Keluar
                    </a>

                    <a href="#"
                       class="flex items-center px-6 py-3 text-sm text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                        <span class="mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4.5 6.75h15M4.5 12h15M4.5 17.25h8.25" />
                                </svg>
                        </span>
                        Disposisi
                    </a>
                </nav>
            </aside>

            {{-- ========== BAGIAN KANAN (TOPBAR + CONTENT) ========== --}}
            <div class="flex-1 flex flex-col">

                {{-- TOPBAR --}}
                <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                    <h1 class="text-lg font-semibold text-gray-800">
                        Dashboard
                    </h1>

                    {{-- Bagian kanan: Teams dropdown + Settings dropdown bawaan Jetstream --}}
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        {{-- === Teams Dropdown (opsional, kalau pakai fitur Team) === --}}
                        @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                            <div class="ms-3 relative">
                                <x-dropdown align="right" width="60">
                                    <x-slot name="trigger">
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ Auth::user()->currentTeam->name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Manage Team') }}
                                            </div>

                                            <!-- Team Settings -->
                                            <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                                {{ __('Team Settings') }}
                                            </x-dropdown-link>

                                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                                <x-dropdown-link href="{{ route('teams.create') }}">
                                                    {{ __('Create New Team') }}
                                                </x-dropdown-link>
                                            @endcan

                                            <!-- Team Switcher -->
                                            @if (Auth::user()->allTeams()->count() > 1)
                                                <div class="border-t border-gray-200"></div>

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    {{ __('Switch Teams') }}
                                                </div>

                                                @foreach (Auth::user()->allTeams() as $team)
                                                    <x-switchable-team :team="$team" />
                                                @endforeach
                                            @endif
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif

                        {{-- === Settings Dropdown (INI YANG PENTING UNTUK 2FA / PROFILE) === --}}
                        <div class="ms-3 relative">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                        <button
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover"
                                                src="{{ Auth::user()->profile_photo_url }}"
                                                alt="{{ Auth::user()->name }}" />
                                        </button>
                                    @else
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                {{ Auth::user()->name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    @endif
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Account') }}
                                    </div>

                                    {{-- Link ke halaman Profile (di sini ada 2FA) --}}
                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                        <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                            {{ __('API Tokens') }}
                                        </x-dropdown-link>
                                    @endif

                                    <div class="border-t border-gray-200"></div>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf

                                        <x-dropdown-link href="{{ route('logout') }}"
                                                        @click.prevent="$root.submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                {{-- CONTENT --}}
                <main class="flex-1 p-6 bg-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

                        {{-- Card Surat Masuk --}}
                        <div class="bg-white rounded-xl shadow p-4 flex items-center">
                            <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M3 7.5l9 6 9-6M4.5 5.25h15v13.5h-15z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Surat Masuk</div>
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ $suratMasuk ?? 0 }}
                                </div>
                            </div>
                        </div>

                        {{-- Card Surat Keluar --}}
                        <div class="bg-white rounded-xl shadow p-4 flex items-center">
                            <div class="h-12 w-12 rounded-xl bg-yellow-100 flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4.5 12h15M12 4.5l7.5 7.5L12 19.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Surat Keluar</div>
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ $suratKeluar ?? 0 }}
                                </div>
                            </div>
                        </div>

                        {{-- Card Akun User --}}
                        <div class="bg-white rounded-xl shadow p-4 flex items-center">
                            <div class="h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Akun User</div>
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ $jumlahUser ?? 0 }}
                                </div>
                            </div>
                        </div>

                        {{-- Card Template Surat --}}
                        <div class="bg-white rounded-xl shadow p-4 flex items-center">
                            <div class="h-12 w-12 rounded-xl bg-pink-100 flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-500" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M4.5 4.5h11.25L19.5 8.25V19.5H4.5z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500">Template Surat</div>
                                <div class="text-2xl font-bold text-gray-800">
                                    {{ $templateSurat ?? 0 }}
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
