<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">
            <x-layouts.sidebar />

            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-20': sidebarCollapsed }">

                <x-layouts.topbar />

                <main class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-800">Jalur Surat</h1>
                            <p class="text-sm text-slate-500">
                                Konfigurasi alur verifikasi dan penandatanganan surat
                            </p>
                        </div>

                        <a href="{{ route('admin.jalur-surat.create') }}"
                           class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">
                            + Tambah Jalur
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($alurs as $alur)
                            <div class="bg-white border rounded-xl shadow-sm p-5">
                                <div class="mb-3">
                                    <h3 class="font-semibold text-slate-800">
                                        {{ $alur->nama_alur }}
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span>{{ $alur->kode_alur }}</span>
                                        @if(!$alur->is_active)
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-700">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <ol class="space-y-2 text-sm">
                                    @foreach($alur->steps as $step)
                                    <tr>
                                        <td>{{ $step->urutan }}</td>
                                        <td>{{ ucfirst($step->unit_scope) }}</td>
                                        <td>
                                            @if($step->perlu_ttd)
                                                <span class="text-green-600">TTD</span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </ol>

                                <div class="flex gap-4 mt-4 text-sm">
                                    <a href="{{ route('admin.jalur-surat.edit', $alur) }}"
                                        class="inline-flex px-3 py-1.5 text-xs
                                        bg-emerald-100 text-emerald-700 rounded-lg">                                        
                                        Edit
                                    </a>

                                    @if($alur->is_active)
                                        <form method="POST"
                                              action="{{ route('admin.jalur-surat.destroy', $alur) }}"
                                              class ="inline-flex px-3 py-1.5 text-xs
                                                bg-yellow-100 text-yellow-700 rounded-lg">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 text-sm"  >
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
