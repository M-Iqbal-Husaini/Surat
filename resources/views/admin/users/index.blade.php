<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">
            {{-- SIDEBAR --}}
            <x-layouts.sidebar />

            {{-- OVERLAY MOBILE --}}
            <div
                class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            {{-- MAIN WRAPPER --}}
            <div
                class="flex-1 flex flex-col w-full min-h-screen transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,   // sidebar lebar
                    'lg:ml-20': sidebarCollapsed     // sidebar kecil
                }"
            >
                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- MAIN CONTENT --}}
                <main class="flex-1 bg-slate-50 p-4 sm:p-6">
                    <div class="w-full space-y-6">
                        <h1 class="text-xl font-semibold text-gray-800">
                            Manajemen User
                        </h1>
                        <div class="py-8">
                            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                <div class="bg-white shadow rounded-lg p-6">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead class="text-left text-gray-500">
                                                <tr>
                                                    <th class="py-2">Nama</th>
                                                    <th class="py-2">Email</th>
                                                    <th class="py-2">Role</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y">
                                                @foreach ($users as $u)
                                                    <tr>
                                                        <td class="py-2">{{ $u->name }}</td>
                                                        <td class="py-2">{{ $u->email }}</td>
                                                        <td class="py-2">
                                                            {{ $u->getRoleNames()->implode(', ') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4">
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
