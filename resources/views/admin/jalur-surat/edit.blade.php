<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="pageState()" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            {{-- SIDEBAR --}}
            <x-layouts.sidebar />

            {{-- MAIN --}}
            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-20': sidebarCollapsed }">

                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- CONTENT --}}
                <main class="p-6 space-y-6">
                    <h1 class="text-xl font-semibold text-slate-800">
                        Edit Jalur Surat
                    </h1>

                    <form method="POST"
                          action="{{ route('admin.jalur-surat.update', $alur) }}"
                          class="bg-white border rounded-xl p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- ERROR --}}
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-300 text-red-700 p-4 rounded">
                                <ul class="list-disc ml-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- HEADER --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">Kode Alur</label>
                                <input
                                    value="{{ $alur->kode_alur }}"
                                    class="w-full mt-1 rounded border-slate-300 bg-slate-100"
                                    disabled>
                            </div>

                            <div>
                                <label class="text-sm font-medium">Nama Alur</label>
                                <input
                                    name="nama_alur"
                                    value="{{ old('nama_alur', $alur->nama_alur) }}"
                                    class="w-full mt-1 rounded border-slate-300"
                                    required>
                            </div>

                            <div>
                                <label class="text-sm font-medium">Status</label>
                                <select
                                    name="is_active"
                                    class="w-full mt-1 rounded border-slate-300">
                                    <option value="1" @selected($alur->is_active)>Aktif</option>
                                    <option value="0" @selected(!$alur->is_active)>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        {{-- DETAIL STEP --}}
                        <div
                            x-data="alurEditSteps(
                                {{ $alur->steps->map(fn ($s) => [
                                    'unit_scope' => $s->unit_scope,
                                    'jabatan_id' => $s->jabatan_id,
                                    'perlu_ttd'  => (bool) $s->perlu_ttd,
                                ])->values()->toJson() }}
                            )"
                            class="space-y-4"
                        >
                            <h3 class="font-semibold text-slate-700">
                                Tahapan Verifikasi
                            </h3>

                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex gap-3 items-center">
                                    <span class="w-6 text-sm font-medium"
                                          x-text="index + 1"></span>

                                    {{-- UNIT SCOPE --}}
                                    <select
                                        :name="`detail[${index}][unit_scope]`"
                                        x-model="step.unit_scope"
                                        class="rounded border-slate-300"
                                        required>
                                        <option value="asal">Unit Asal</option>
                                        <option value="tujuan">Unit Tujuan</option>
                                    </select>

                                    <select
                                        :name="`detail[${index}][jabatan_id]`"
                                        x-model="step.jabatan_id"
                                        class="rounded border-slate-300"
                                        required>
                                        <option value="">— Pilih Jabatan —</option>
                                        @foreach($jabatans as $jabatan)
                                            <option value="{{ $jabatan->id }}">
                                                {{ $jabatan->unit?->nama_unit ?? '-' }} — {{ $jabatan->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>


                                    {{-- PERLU TTD --}}
                                    <label class="flex items-center gap-1 text-sm">
                                        <input
                                            type="checkbox"
                                            :name="`detail[${index}][perlu_ttd]`"
                                            value="1"
                                            x-model="step.perlu_ttd">
                                        TTD
                                    </label>

                                    {{-- REMOVE --}}
                                    <button type="button"
                                            x-show="steps.length > 1"
                                            @click="remove(index)"
                                            class="text-red-600 text-sm">
                                        Hapus
                                    </button>
                                </div>
                            </template>

                            <button type="button"
                                    @click="add"
                                    class="text-sm text-blue-600">
                                + Tambah Tahap
                            </button>
                        </div>

                        {{-- ACTION --}}
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.jalur-surat.index') }}"
                               class="px-4 py-2 rounded border border-slate-300">
                                Batal
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded bg-blue-600 text-white">
                                Update
                            </button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function pageState() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
            }
        }

        function alurEditSteps(initialSteps) {
            return {
                steps: initialSteps.length
                    ? initialSteps
                    : [{
                        unit_scope: 'asal',
                        perlu_ttd: false,
                    }],

                add() {
                    this.steps.push({
                        unit_scope: 'asal',
                        perlu_ttd: false,
                    })
                },

                remove(index) {
                    this.steps.splice(index, 1)
                }
            }
        }
    </script>
</x-app-layout>
