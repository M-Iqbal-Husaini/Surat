<x-app-layout>
<x-slot name="header"></x-slot>

<div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
    <div class="flex min-h-screen">

        <x-layouts.sidebar />

        <div
            class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-cloak
        ></div>

        <div
            class="flex-1 flex flex-col transition-all duration-300"
            :class="{
                'lg:ml-72': !sidebarCollapsed,
                'lg:ml-20': sidebarCollapsed
            }"
        >
            <x-layouts.topbar />

            <main class="flex-1 p-6 space-y-6">

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Detail Surat Masuk
                        </h1>
                        <p class="text-sm text-slate-500">
                            Pratinjau dan tindak lanjut surat masuk
                        </p>
                    </div>

                    <a
                        href="{{ route('pembuat-surat.surat-masuk.index') }}"
                        class="px-4 py-2 rounded-lg border text-slate-600 hover:bg-slate-100"
                    >
                        ← Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm">
                        <div class="p-6 overflow-auto max-h-[75vh]">
                            <div class="mx-auto bg-gray-100 p-6 rounded-lg" style="max-width: 21cm">
                                <div class="mx-auto bg-white shadow" style="min-height: 29.7cm">
                                    @include('partials.surat-preview', [
                                        'template'    => $surat->template,
                                        'previewHtml' => $previewHtml,
                                        'surat'       => $surat
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">

                        <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3 text-sm">
                            <div>
                                <span class="text-slate-500">Asal Surat</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->unitAsal?->nama_unit ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <span class="text-slate-500">Jenis Surat</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->template->nama_template }}
                                </p>
                            </div>

                            <div>
                                <span class="text-slate-500">Tanggal Surat</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->tanggal_surat?->format('d M Y') }}
                                </p>
                            </div>

                            @php
                                [$label, $badge] = match($surat->status) {
                                    'final'     => ['Final', 'bg-green-100 text-green-700'],
                                    'ditolak'   => ['Ditolak', 'bg-red-100 text-red-700'],
                                    'direvisi'  => ['Direvisi', 'bg-orange-100 text-orange-700'],
                                    'disposisi' => ['Disposisi', 'bg-indigo-100 text-indigo-700'],
                                    default     => ['Diproses', 'bg-blue-100 text-blue-700'],
                                };
                            @endphp

                            <div>
                                <span class="text-slate-500">Status Surat</span><br>
                                <span class="inline-block px-2 py-1 text-xs rounded {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border shadow-sm p-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                                Alur Verifikasi
                            </h3>

                            <ol class="space-y-4">
                                @foreach($alurSteps as $step)
                                    @php
                                        [$dot, $label] = match($step['state']) {
                                            'done'      => ['bg-emerald-500', 'Selesai'],
                                            'active'    => ['bg-blue-500 animate-pulse', 'Diproses'],
                                            'direvisi'  => ['bg-orange-500', 'Direvisi'],
                                            'disposisi' => ['bg-indigo-500', 'Disposisi'],
                                            'ditolak'   => ['bg-red-500', 'Ditolak'],
                                            default     => ['bg-slate-300', 'Menunggu'],
                                        };
                                    @endphp

                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 w-3 h-3 rounded-full {{ $dot }}"></span>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $step['jabatan'] }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ $label }}
                                                @if($step['perlu_ttd'])
                                                    • Perlu TTD
                                                @endif
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        @if($disposisiAktif)
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 text-sm">
                            <p class="font-medium text-indigo-700">
                                Disposisi ke: {{ $disposisiAktif->keJabatan?->nama_jabatan ?? '-' }}
                            </p>
                            <p class="mt-1 text-slate-600">
                                Catatan: {{ $disposisiAktif->instruksi }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Status: {{ ucfirst($disposisiAktif->status) }}
                            </p>
                        </div>
                        @endif

                        <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3">

                            @if($canApprove)
                                <form method="POST"
                                      action="{{ route('pembuat-surat.surat-masuk.terima', $surat) }}">
                                    @csrf
                                    <button class="w-full px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-lg">
                                        Terima Surat
                                    </button>
                                </form>
                            @endif

                            @if($canFinalize)
                                <form method="POST"
                                      action="{{ route('pembuat-surat.surat-masuk.finalisasi', $surat) }}">
                                    @csrf
                                    <button class="w-full px-3 py-1.5 text-sm bg-emerald-200 text-emerald-900 rounded-lg">
                                        Finalisasi Surat
                                    </button>
                                </form>
                            @endif

                            @if($canDispose)
                                <button
                                    type="button"
                                    onclick="openDisposisiModal()"
                                    class="w-full px-3 py-1.5 text-sm bg-indigo-100 text-indigo-700 rounded-lg">
                                    Disposisi
                                </button>
                            @endif

                            @if($canFollowUp)
                                <form method="POST"
                                      action="{{ route('pembuat-surat.surat-masuk.tindaklanjuti', $surat) }}">
                                    @csrf
                                    <button class="w-full px-3 py-1.5 text-sm bg-sky-100 text-sky-700 rounded-lg">
                                        Tindaklanjuti Disposisi
                                    </button>
                                </form>
                            @endif

                            @if($canRevise)
                                <button
                                    type="button"
                                    onclick="openRevisiModal()"
                                    class="w-full px-3 py-1.5 text-sm bg-amber-100 text-amber-800 rounded-lg">
                                    Kembalikan untuk Revisi
                                </button>
                            @endif

                            @if($canReject)
                                <form method="POST"
                                      action="{{ route('pembuat-surat.surat-masuk.tolak', $surat) }}">
                                    @csrf
                                    <button class="w-full px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg">
                                        Tolak Surat
                                    </button>
                                </form>
                            @endif

                            @if(
                                !$canApprove &&
                                !$canFinalize &&
                                !$canDispose &&
                                !$canFollowUp &&
                                !$canRevise &&
                                !$canReject
                            )
                                <div class="text-sm text-slate-500 text-center">
                                    Tidak ada aksi yang dapat dilakukan.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<div id="disposisiModal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Disposisi Surat</h3>

        <form method="POST"
              action="{{ route('pembuat-surat.surat-masuk.disposisi', $surat) }}"
              class="space-y-4">
            @csrf

            <textarea
                name="catatan_disposisi"
                required
                rows="3"
                class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>

            <select
                name="ke_jabatan_id"
                required
                class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="">-- Pilih Jabatan --</option>
                @foreach($jabatanTujuan as $jabatan)
                    <option value="{{ $jabatan->id }}">
                        {{ $jabatan->nama_jabatan }}
                    </option>
                @endforeach
            </select>

            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="closeDisposisiModal()"
                        class="px-4 py-2 bg-slate-500 text-white rounded-lg">
                    Batal
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                    Teruskan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="revisiModal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-slate-800">
            Catatan Revisi Surat
        </h3>

        <form method="POST"
              action="{{ route('pembuat-surat.surat-masuk.direvisi', $surat) }}"
              class="space-y-4">
            @csrf

            <textarea
                name="catatan"
                required
                rows="4"
                class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>

            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="closeRevisiModal()"
                        class="px-4 py-2 bg-slate-500 text-white rounded-lg">
                    Batal
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg">
                    Kirim Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDisposisiModal(){document.getElementById('disposisiModal').classList.remove('hidden')}
function closeDisposisiModal(){document.getElementById('disposisiModal').classList.add('hidden')}
function openRevisiModal(){document.getElementById('revisiModal').classList.remove('hidden')}
function closeRevisiModal(){document.getElementById('revisiModal').classList.add('hidden')}
</script>

<style>[x-cloak]{display:none!important}</style>
</x-app-layout>
