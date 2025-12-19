<header class="h-16 bg-white border-b flex items-center justify-between px-4 lg:px-8">

    {{-- Left --}}
    <div class="flex items-center space-x-3">
        <button class="lg:hidden" @click="openSidebar = true">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="hidden lg:flex items-center space-x-2">
            <div class="bg-indigo-600 text-white h-8 w-8 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M4 4h16v16H4z" />
                </svg>
            </div>
            <span class="text-lg font-semibold text-gray-700">E-Surat</span>
        </div>
    </div>

    {{-- Right --}}
    <div class="flex items-center space-x-4">
        <button class="p-2 rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3v1m6.36 1.64l-.7.7M21 12h-1M4 12H3m2.34 6.36l-.7-.7M12 21v-1m6.36-1.64l-.7-.7" />
            </svg>
        </button>

        <button class="relative p-2 rounded-full hover:bg-gray-100">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h5l-1.4-1.4A2 2 0 0118 14V11a6 6 0 10-12 0v3c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0" />
            </svg>

            <span class="absolute top-0 right-0 h-2.5 w-2.5 bg-red-500 border-2 border-white rounded-full"></span>
        </button>

        {{-- PROFILE DROPDOWN --}}
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center space-x-2">
                    <img class="w-9 h-9 rounded-full object-cover"
                        src="{{ Auth::user()->profile_photo_url }}">
                    <span class="text-gray-700 font-medium hidden sm:block">
                        {{ Auth::user()->name }}
                    </span>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link href="{{ route('profile.show') }}">
                    Profile
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-dropdown-link href="{{ route('logout') }}"
                                     @click.prevent="$root.submit();">
                        Log Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
