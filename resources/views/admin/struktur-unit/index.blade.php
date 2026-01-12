<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            <x-layouts.sidebar />

            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-20': sidebarCollapsed }">

                <x-layouts.topbar />

                <main class="p-6 space-y-6">

                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-800">Struktur Unit</h1>
                            <p class="text-sm text-slate-500">
                                Daftar unit kerja beserta relasinya
                            </p>
                        </div>

                        <a href="{{ route('admin.struktur-unit.create') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded">
                            + Tambah Unit
                        </a>
                    </div>

                    <div class="bg-white rounded-xl border overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kode</th>
                                    <th class="px-4 py-3 text-left">Nama</th>
                                    <th class="px-4 py-3 text-left">Jenis</th>
                                    <th class="px-4 py-3 text-center">Jabatan</th>
                                    <th class="px-4 py-3 text-center">User</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($units as $unit)
                                    <tr>
                                        <td class="px-4 py-3 font-mono">{{ $unit->kode_unit }}</td>
                                        <td class="px-4 py-3">{{ $unit->nama_unit }}</td>
                                        <td class="px-4 py-3">{{ ucfirst($unit->jenis_unit) }}</td>
                                        <td class="px-4 py-3 text-center">{{ $unit->jabatans_count }}</td>
                                        <td class="px-4 py-3 text-center">{{ $unit->users_count }}</td>
                                        <td class="px-4 py-3 text-right space-x-2">                          
                                            <a href="{{ route('admin.struktur-unit.show', $unit) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                        bg-indigo-100 text-indigo-700 text-xs hover:bg-indigo-200">
                                                Detail</a>
                                            <a href="{{ route('admin.struktur-unit.edit', $unit) }}"
                                                class="inline-flex px-3 py-1.5 text-xs
                                                        bg-emerald-100 text-emerald-700 rounded-lg">
                                                Edit</a>

                                            <form method="POST"
                                                  action="{{ route('admin.struktur-unit.destroy', $unit) }}"
                                                class="inline-flex px-3 py-1.5 text-xs
                                                        bg-yellow-100 text-yellow-700 rounded-lg"                                                
                                                        onsubmit="return confirm('Hapus unit ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 text-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
