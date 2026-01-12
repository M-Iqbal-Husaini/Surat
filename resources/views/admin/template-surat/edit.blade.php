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

                    {{-- TITLE --}}
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-slate-800">
                            Edit Template Surat
                        </h1>

                        <a href="{{ route('admin.template-surat.index', $template->id) }}"
                           class="px-4 py-2 rounded-lg bg-slate-200 text-sm">
                            ← Kembali
                        </a>
                    </div>

                    {{-- FORM CARD --}}
                    <div class="bg-white rounded-xl border shadow-sm max-w-2xl">
                        <form method="POST"
                              action="{{ route('admin.template-surat.update', $template->id) }}"
                              class="p-6 space-y-6">
                            @csrf
                            @method('PUT')

                            {{-- KODE TEMPLATE --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Kode Template
                                </label>
                                <input
                                    type="text"
                                    name="kode_template"
                                    value="{{ old('kode_template', $template->kode_template) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                @error('kode_template')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- NAMA TEMPLATE --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Nama Template
                                </label>
                                <input
                                    type="text"
                                    name="nama_template"
                                    value="{{ old('nama_template', $template->nama_template) }}"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                @error('nama_template')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- JENIS SURAT --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Jenis Surat
                                </label>
                                <select
                                    name="jenis_surat_id"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    @foreach($jenisSurat as $js)
                                        <option value="{{ $js->id }}"
                                            {{ old('jenis_surat_id', $template->jenis_surat_id) == $js->id ? 'selected' : '' }}>
                                            {{ $js->kode }} — {{ $js->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_surat_id')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- PENANDATANGAN --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Penandatangan
                                </label>
                                <select name="penandatangan_jabatan_id"
                                        class="w-full rounded-lg border px-3 py-2">
                                    <option value="">-- Default Sistem --</option>
                                    @foreach($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}">
                                            {{ $jabatan->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- JALUR SURAT --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Jalur Surat
                                </label>
                                <select
                                    name="kode_alur"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    @foreach($alurVerifikasi as $alur)
                                        <option value="{{ $alur->kode_alur }}"
                                            {{ old('kode_alur', $template->kode_alur) == $alur->kode_alur ? 'selected' : '' }}>
                                            {{ $alur->nama_alur }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kode_alur')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- STATUS --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Status Template
                                </label>
                                <select
                                    name="is_active"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2
                                           focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="1" {{ old('is_active', $template->is_active) == 1 ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="0" {{ old('is_active', $template->is_active) == 0 ? 'selected' : '' }}>
                                        Nonaktif
                                    </option>
                                </select>
                                @error('is_active')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- ACTION --}}
                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.template-surat.show', $template->id) }}"
                                   class="px-4 py-2 rounded-lg border border-slate-300
                                          text-slate-700 hover:bg-slate-100">
                                    Batal
                                </a>

                                <button
                                    type="submit"
                                    class="px-6 py-2 rounded-lg bg-indigo-600 text-white
                                           hover:bg-indigo-700">
                                    Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
