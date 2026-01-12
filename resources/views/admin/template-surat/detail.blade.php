<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            {{-- SIDEBAR --}}
            <x-layouts.sidebar />

            {{-- OVERLAY MOBILE --}}
            <div class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                 x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 x-cloak></div>

            {{-- MAIN --}}
            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                 }">

                <x-layouts.topbar />

                {{-- CONTENT --}}
                <main class="p-6 space-y-6">

                    {{-- TITLE --}}
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-slate-800">
                            Detail Template Surat
                        </h1>

                        <a href="{{ route('admin.template-surat.index') }}"
                           class="px-4 py-2 rounded-lg bg-slate-200 text-sm">
                            ← Kembali
                        </a>
                    </div>

                    {{-- ================= CARD DETAIL ================= --}}
                    <div class="bg-white border rounded-xl p-6 space-y-6 max-w-5xl">

                        {{-- INFO UTAMA --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">

                            <div>
                                <div class="text-slate-500">Kode Template</div>
                                <div class="font-semibold text-slate-800">
                                    {{ $template->kode_template }}
                                </div>
                            </div>

                            <div>
                                <div class="text-slate-500">Nama Template</div>
                                <div class="font-semibold text-slate-800">
                                    {{ $template->nama_template }}
                                </div>
                            </div>

                            <div>
                                <div class="text-slate-500">Jenis Surat</div>
                                <div>
                                    {{ $template->jenisSurat->kode }} —
                                    {{ $template->jenisSurat->nama }}
                                </div>
                            </div>

                            <div>
                                <div class="text-slate-500">Jalur Verifikasi</div>
                                <div>
                                    {{ $template->alurVerifikasi->nama_alur }}
                                </div>
                            </div>

                            <div>
                                <div class="text-slate-500">Penandatangan Default</div>
                                <div class="font-semibold">
                                    {{ optional($template->penandatanganJabatan)->nama_jabatan
                                        ?? 'Mengikuti Disposisi / Default Sistem' }}
                                </div>
                            </div>

                            @php
                                $statusClass = $template->is_active
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700';

                                $statusText = $template->is_active
                                    ? 'Aktif'
                                    : 'Nonaktif';
                            @endphp

                            <div>
                                <div class="text-slate-500">Status</div>
                                <span class="inline-flex px-2 py-1 rounded text-xs {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            <div>
                                <div class="text-slate-500">Dibuat</div>
                                <div>
                                    {{ $template->created_at->translatedFormat('d F Y H:i') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-slate-500">Terakhir Diperbarui</div>
                                <div>
                                    {{ $template->updated_at->translatedFormat('d F Y H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- ================= FIELD TEMPLATE ================= --}}
                        <div class="pt-6 border-t">
                            <h2 class="text-sm font-semibold text-slate-800 mb-3">
                                Struktur Field Surat
                            </h2>

                            @if($template->fields->isEmpty())
                                <div class="text-sm text-slate-500 italic">
                                    Tidak ada field terdaftar untuk template ini.
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm border border-slate-200 rounded-lg">
                                        <thead class="bg-slate-50 text-slate-600">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Urutan</th>
                                                <th class="px-3 py-2 text-left">Placeholder</th>
                                                <th class="px-3 py-2 text-left">Label</th>
                                                <th class="px-3 py-2 text-left">Tipe</th>
                                                <th class="px-3 py-2 text-center">Wajib</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($template->fields->sortBy('order') as $field)
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">{{ $field->order }}</td>
                                                    <td class="px-3 py-2 font-mono text-indigo-600">
                                                    @php
                                                        echo '{{' . $field->field_key . '}}';
                                                    @endphp
                                                    </td>
                                                    <td class="px-3 py-2">{{ $field->label }}</td>
                                                    <td class="px-3 py-2 uppercase">{{ $field->type }}</td>
                                                    <td class="px-3 py-2 text-center">
                                                        @if($field->required)
                                                            <span class="text-green-600 font-semibold">Ya</span>
                                                        @else
                                                            <span class="text-slate-400">Tidak</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- ================= PLACEHOLDER ================= --}}
                        <div class="pt-6 border-t">
                            <h2 class="text-sm font-semibold text-slate-800 mb-2">
                                Placeholder Tersedia
                            </h2>

                            <div class="flex flex-wrap gap-2 text-xs">
                                @foreach($template->fields as $field)
                                    <span class="px-2 py-1 rounded bg-slate-100
                                                 text-slate-700 font-mono">
                                            @php
                                                echo '{{' . $field->field_key . '}}';
                                            @endphp
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- ================= INFO TEKNIS ================= --}}
                        <div class="pt-6 border-t">
                            <h2 class="text-sm font-semibold text-slate-800 mb-2">
                                Informasi Teknis
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-500">ID Template:</span>
                                    {{ $template->id }}
                                </div>
                                <div>
                                    <span class="text-slate-500">Kode Jenis Surat:</span>
                                    {{ $template->jenisSurat->kode }}
                                </div>
                                <div>
                                    <span class="text-slate-500">Jumlah Field:</span>
                                    {{ $template->fields->count() }}
                                </div>
                                <div>
                                    <span class="text-slate-500">Kode Alur:</span>
                                    {{ $template->kode_alur }}
                                </div>
                            </div>
                        </div>

                        {{-- ================= ACTION ================= --}}
                        <div class="flex justify-between items-center pt-6 border-t">
                            <div class="flex gap-2">
                                <button onclick="openPreview()"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                                    Preview Surat
                                </button>

                                <a href="{{ route('admin.template-surat.edit', $template->id) }}"
                                   class="px-4 py-2 bg-yellow-400 rounded-lg text-sm">
                                    Edit Template
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    {{-- ================= MODAL PREVIEW ================= --}}
    <div id="previewModal"
         class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="bg-white w-[90%] max-w-5xl rounded-xl overflow-hidden">
            <div class="flex justify-between items-center px-4 py-2 border-b">
                <div class="text-sm font-semibold">Preview Template Surat</div>
                <button onclick="closePreview()" class="text-lg">✖</button>
            </div>

            <div class="overflow-auto max-h-[85vh]">
                @include('admin.template-surat.preview', ['template' => $template])
            </div>
        </div>
    </div>

    {{-- ================= SCRIPT ================= --}}
    <script>
        function openPreview() {
            const modal = document.getElementById('previewModal')
            modal.classList.remove('hidden')
            modal.classList.add('flex')
        }

        function closePreview() {
            const modal = document.getElementById('previewModal')
            modal.classList.add('hidden')
            modal.classList.remove('flex')
        }
    </script>
</x-app-layout>
