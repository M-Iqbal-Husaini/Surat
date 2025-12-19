{{-- resources/views/components/layouts/topbar.blade.php --}}
<header class="sticky top-0 z-30 h-16 w-full bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6">
    {{-- KIRI: tombol menu --}}
    <div class="flex items-center space-x-3">
        <button
            class="p-2 rounded-lg hover:bg-slate-100"
            @click="
                if (window.innerWidth < 1024) {
                    sidebarOpen = !sidebarOpen
                } else {
                    sidebarCollapsed = !sidebarCollapsed
                }
            "
        >
            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.8"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>
        </button>
    </div>

    {{-- KANAN: profil --}}
    <div class="s-3 relative mr-1">
        <x-dropdown align="right" width="48">
            {{-- Trigger: teks nama + ikon dropdown --}}
            <x-slot name="trigger">
                <button
                    type="button"
                    class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-slate-800 focus:outline-none"
                >
                    <span class="me-1">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="transition-transform" :class="open ? 'rotate-180' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </span>

                </button>
            </x-slot>

            {{-- Isi dropdown --}}
            <x-slot name="content">
                <div class="block px-4 py-2 text-[14px] font-medium text-gray-600">
                    {{ __('Account') }}
                </div>

                <x-dropdown-link href="{{ route('profile.show') }}">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <div class="border-t border-gray-200"></div>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-dropdown-link
                        href="{{ route('logout') }}"
                        @click.prevent="$root.submit();"
                    >
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
