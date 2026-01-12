<x-app-layout>
<x-slot name="header"></x-slot>

<div x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
     class="min-h-screen bg-slate-50">
    <div class="flex min-h-screen">

        <x-layouts.sidebar />

        <div class="flex-1 flex flex-col transition-all duration-300"
             :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-20': sidebarCollapsed }">

            <x-layouts.topbar />

            <main class="p-6 space-y-6">

                {{-- HEADER --}}
                <div>
                    <h1 class="text-xl font-semibold text-slate-800">
                        Edit Surat – {{ $surat->template->nama_template }}
                    </h1>
                    @php
                        $statusLabel = [
                            'draft'     => 'Draft',
                            'direvisi'  => 'Dikembalikan untuk Revisi',
                            'diajukan'  => 'Menunggu Proses',
                            'diproses'  => 'Sedang Diproses',
                            'final'     => 'Selesai',
                            'ditolak'   => 'Ditolak',
                        ];
                    @endphp

                    <p class="text-sm text-slate-500">
                        Status:
                        <span class="font-medium text-orange-600">
                            {{ $statusLabel[$surat->status] ?? ucfirst($surat->status) }}
                        </span>
                    </p>
                </div>

                {{-- FORM --}}
                <form method="POST"
                      action="{{ route('pembuat-surat.surat-keluar.update', $surat) }}"
                      class="bg-white rounded-xl border shadow-sm p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- INFO --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-500">Template</span>
                            <p class="font-medium text-slate-800">
                                {{ $surat->template->nama_template }}
                            </p>
                        </div>
                        <div>
                            <span class="text-slate-500">Jenis Surat</span>
                            <p class="font-medium text-slate-800">
                                {{ $surat->template->jenisSurat->nama }}
                            </p>
                        </div>
                    </div>

                    <hr>
                        {{-- PERIHAL --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Perihal <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="perihal"
                                value="{{ old('perihal', $surat->perihal) }}"
                                required
                                maxlength="255"
                                class="w-full rounded-lg border-slate-300
                                    focus:border-blue-500 focus:ring-blue-500">

                        </div>

                    {{-- FIELD DINAMIS --}}
                    @foreach ($surat->template->fields as $field)
                        @php
                            $value = $surat->data_json[$field->field_key] ?? '';
                        @endphp

                        <div>
                            <label class="block text-sm text-slate-600 mb-1">
                                {{ $field->label }}
                                @if ($field->required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            <input
                                type="text"
                                name="data[{{ $field->field_key }}]"
                                value="{{ old('data.' . $field->field_key, $value) }}"
                                class="w-full rounded-lg border-slate-300"
                                {{ $field->required ? 'required' : '' }}
                                {{ $editable ? '' : 'readonly' }}>
                        </div>
                    @endforeach

                    <div class="flex justify-end gap-2 pt-4">
                        <a href="{{ route('pembuat-surat.surat-keluar.show', $surat) }}"
                        class="px-4 py-2
                                border border-slate-300
                                text-slate-600
                                hover:bg-slate-100
                                rounded-lg
                                text-sm
                                transition">
                            Kembali
                        </a>

                        @if($editable)
                            <button
                                class="px-6 py-2
                                    bg-blue-600 hover:bg-blue-700
                                    focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                    text-white text-sm font-medium
                                    rounded-lg
                                    transition">
                                Simpan Perubahan
                            </button>
                        @endif
                    </div>

                </form>

            </main>
        </div>
    </div>
</div>
</x-app-layout>
