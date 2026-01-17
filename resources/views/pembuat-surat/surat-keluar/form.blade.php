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
                            Buat {{ $template->nama_template }}
                        </h1>
                        <p class="text-sm text-slate-500">
                            Surat antar unit – tentukan unit tujuan dan alur verifikasi
                        </p>
                    </div>

                    {{-- FORM --}}
                    <form method="POST"
                          action="{{ route('pembuat-surat.surat-keluar.store') }}"
                          class="bg-white rounded-xl border shadow-sm p-6 space-y-5">
                        @csrf

                        <input type="hidden"
                               name="template_id"
                               value="{{ $template->id }}">

                        {{-- UNIT TUJUAN --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Unit Tujuan <span class="text-red-500">*</span>
                            </label>

                            <select name="unit_tujuan_id"
                                    required
                                    class="w-full rounded-lg border-slate-300">
                                <option value="">— Pilih Unit Tujuan —</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        @selected(old('unit_tujuan_id') == $unit->id)>
                                        {{ $unit->nama_unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ALUR VERIFIKASI --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Alur Verifikasi <span class="text-red-500">*</span>
                            </label>

                            <select name="alur_id"
                                    required
                                    class="w-full rounded-lg border-slate-300">
                                <option value="">— Pilih Alur Verifikasi —</option>
                                @foreach ($alurs as $alur)
                                    <option value="{{ $alur->id }}"
                                        @selected(old('alur_id') == $alur->id)>
                                        {{ $alur->nama_alur }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="text-xs text-slate-500 mt-1">
                                * Hanya alur dengan jabatan verifikator yang dapat dipilih
                            </p>
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
                                value="{{ old('perihal') }}"
                                required
                                maxlength="255"
                                placeholder="Contoh: Permohonan Data Mahasiswa"
                                class="w-full rounded-lg border-slate-300
                                    focus:border-blue-500 focus:ring-blue-500">
                        </div>


                        {{-- FIELD DINAMIS --}}
                        @foreach ($template->fields as $field)
                            <div>
                                <label class="block text-sm text-slate-600 mb-1">
                                    {{ $field->label }}
                                    @if ($field->required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @php
                                    $inputType = match ($field->type) {
                                        'date'     => 'date',
                                        'number'   => 'number',
                                        'text'     => 'text',
                                        'textarea' => 'textarea',
                                        default    => 'text',
                                    };
                                @endphp

                                @if ($inputType === 'textarea')
                                    <textarea
                                        name="data[{{ $field->field_key }}]"
                                        rows="4"
                                        class="w-full rounded-lg border-slate-300
                                            focus:border-blue-500 focus:ring-blue-500"
                                        {{ $field->required ? 'required' : '' }}
                                    >{{ old('data.' . $field->field_key) }}</textarea>
                                @else
                                    <input
                                        type="{{ $inputType }}"
                                        name="data[{{ $field->field_key }}]"
                                        value="{{ old('data.' . $field->field_key) }}"
                                        class="w-full rounded-lg border-slate-300
                                            focus:border-blue-500 focus:ring-blue-500"
                                        {{ $field->required ? 'required' : '' }}>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex justify-end gap-2 pt-4">
                            <a href="{{ route('pembuat-surat.surat-keluar.index') }}"
                            class="px-4 py-2
                                    border border-slate-300
                                    text-slate-600
                                    hover:bg-slate-100
                                    rounded-lg
                                    text-sm
                                    transition">
                                Batal
                            </a>

                            <button
                                class="px-6 py-2
                                    bg-blue-600 hover:bg-blue-700
                                    focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                    text-white text-sm font-medium
                                    rounded-lg
                                    transition">
                                Simpan Surat
                            </button>
                        </div>

                    </form>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
