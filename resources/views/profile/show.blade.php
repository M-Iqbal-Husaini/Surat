{{-- resources/views/profile/show.blade.php --}}
<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- Wrapper dengan Alpine state (sidebarOpen & sidebarCollapsed) --}}
    <div
        x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
        class="min-h-screen bg-slate-50"
    >
        <div class="flex min-h-screen">
            {{-- SIDEBAR --}}
            <x-layouts.sidebar />
            <div
                class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            <div
                class="flex-1 flex flex-col transition-[margin] duration-200"
                :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-72'"
            >
                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- CONTENT PROFILE --}}
                <main class="flex-1 bg-gray-100 p-6">
                    <div class="w-full space-y-6">
                        <h1 class="text-xl font-semibold text-gray-800">
                            Profil Pengguna
                        </h1>

                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            <div class="bg-white rounded-2xl shadow-sm p-6">
                                @livewire('profile.update-profile-information-form')
                            </div>
                        @endif

                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                            <div class="bg-white rounded-2xl shadow-sm p-6">
                                @livewire('profile.update-password-form')
                            </div>
                        @endif

                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                            <div class="bg-white rounded-2xl shadow-sm p-6">
                                @livewire('profile.two-factor-authentication-form')
                            </div>
                        @endif

                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            @livewire('profile.logout-other-browser-sessions-form')
                        </div>

                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                            <div class="bg-white rounded-2xl shadow-sm p-6">
                                @livewire('profile.delete-user-form')
                            </div>
                        @endif
                    </div>
                </main>
            </div> 
        </div> 
    </div> 
</x-app-layout>
