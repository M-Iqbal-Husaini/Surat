<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
         class="min-h-screen bg-slate-50">

        <div class="flex min-h-screen">

            {{-- SIDEBAR --}}
            <x-layouts.sidebar />

            {{-- OVERLAY MOBILE --}}
            <div
                class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                x-cloak>
            </div>

            {{-- MAIN --}}
            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                 }">

                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- CONTENT --}}
                <main class="flex-1 p-6 space-y-6">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-semibold text-slate-800">
                            Manajemen User
                        </h1>

                        <a href="{{ route('admin.users.create') }}"
                           class="px-4 py-2 rounded bg-blue-600 text-white">
                            + Tambah User
                        </a>
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white border rounded-xl p-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-slate-500 border-b">
                                <tr>
                                    <th class="py-2 text-left">Nama</th>
                                    <th class="py-2 text-left">Email</th>
                                    <th class="py-2 text-left">Unit</th>
                                    <th class="py-2 text-left">Jabatan</th>
                                    <th class="py-2 text-left">Role</th>
                                    <th class="py-2 text-left">TTD</th>
                                    <th class="py-2 text-left">Status</th>
                                    <th class="py-2 text-right">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse ($users as $u)
                                    <tr>
                                        <td class="py-2 font-medium">
                                            {{ $u->name }}
                                        </td>

                                        <td class="py-2">
                                            {{ $u->email }}
                                        </td>

                                        <td class="py-2">
                                            {{ $u->unit?->nama_unit ?? '-' }}
                                        </td>

                                        <td class="py-2">
                                            {{ $u->jabatan?->nama_jabatan ?? '-' }}
                                        </td>

                                        <td class="py-2">
                                            {{ $u->roles->pluck('name')->implode(', ') }}
                                        </td>

                                        <td class="py-2">
                                            @if($u->ttd_path)
                                                <span class="text-green-600 text-xs font-semibold">
                                                    Ada
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">
                                                    Belum
                                                </span>
                                            @endif
                                        </td>

                                        <td class="py-2">
                                            <span class="px-2 py-1 text-xs rounded
                                                {{ $u->status === 'aktif'
                                                    ? 'bg-green-100 text-green-700'
                                                    : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($u->status) }}
                                            </span>
                                        </td>

                                        <td class="py-2 text-right space-x-2">
                                            <a href="{{ route('admin.users.show', $u) }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                        bg-indigo-100 text-indigo-700 text-xs hover:bg-indigo-200">
                                                Detail
                                            </a>

                                            <a href="{{ route('admin.users.edit', $u) }}"
                                               class="inline-flex px-3 py-1.5 text-xs
                                                        bg-emerald-100 text-emerald-700 rounded-lg">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="py-6 text-center text-slate-500">
                                            Belum ada user
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- PAGINATION --}}
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
