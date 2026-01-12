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
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            {{-- MAIN --}}
            <div
                class="flex-1 flex flex-col transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                }"
            >
                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- CONTENT --}}
                <main class="p-6 space-y-6">

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.template-surat.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                            Tambah Template
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 text-green-700 px-4 py-2 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="bg-white rounded-xl border overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="px-4 py-3 text-left">Kode</th>
                                    <th class="px-4 py-3 text-left">Nama Template</th>
                                    <th class="px-4 py-3 text-left">Jenis Surat</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($templates as $t)
                                    <tr>
                                        <td class="px-4 py-3">
                                            {{ $t->kode_template }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $t->nama_template }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $t->jenisSurat->kode }} - {{ $t->jenisSurat->nama }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($t->is_active)
                                                <span class="text-green-600 font-medium">Aktif</span>
                                            @else
                                                <span class="text-red-600 font-medium">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                           <div class="flex justify-center gap-2">

                                                {{-- DETAIL --}}
                                                <a href="{{ route('admin.template-surat.show', $t->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                        bg-indigo-100 text-indigo-700 text-xs hover:bg-indigo-200">
                                                    Detail
                                                </a>

                                                <a href="{{ route('admin.template-surat.template-fields.index', $t->id) }}"
                                                class="inline-flex px-3 py-1.5 text-xs
                                                        bg-emerald-100 text-emerald-700 rounded-lg">
                                                    Fields
                                                </a>

                                                {{-- EDIT --}}
                                                <a href="{{ route('admin.template-surat.edit', $t->id) }}"
                                                class="inline-flex px-3 py-1.5 text-xs
                                                        bg-yellow-100 text-yellow-700 rounded-lg">
                                                    Edit
                                                </a>

                                                {{-- DELETE --}}
                                                <form action="{{ route('admin.template-surat.destroy', $t->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Yakin ingin menghapus template ini?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-lg
                                                                bg-red-100 text-red-700 text-xs hover:bg-red-200">
                                                        Hapus
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="px-4 py-6 text-center text-slate-400">
                                            Belum ada template surat
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
