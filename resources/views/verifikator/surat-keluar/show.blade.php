<x-app-layout>
<x-slot name="header"></x-slot>

<div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <x-layouts.sidebar />

        {{-- OVERLAY --}}
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
            <x-layouts.topbar />

            <main class="flex-1 p-6 space-y-6">

                {{-- HEADER --}}
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Detail Surat Keluar (Arsip Verifikator)
                        </h1>
                        <p class="text-sm text-slate-500">
                            Surat yang telah Anda verifikasi atau proses
                        </p>
                    </div>

                    <a href="{{ route('verifikator.surat-keluar.index') }}"
                       class="px-4 py-2 rounded-lg border text-slate-600 hover:bg-slate-100">
                        ← Kembali
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- ================= LEFT : PREVIEW ================= --}}
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

                    {{-- ================= RIGHT ================= --}}
                    <div class="space-y-6">

                        {{-- INFO --}}
                        <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3 text-sm">

                            <div>
                                <span class="text-slate-500">Pembuat</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->pembuat->name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <span class="text-slate-500">Unit Asal</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->unitAsal?->nama_unit ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <span class="text-slate-500">Unit Tujuan</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->unitTujuan?->nama_unit ?? '-' }}
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
                                    {{ $surat->tanggal_surat?->format('d M Y') ?? '-' }}
                                </p>
                            </div>

                            {{-- STATUS --}}
                            <div>
                                <span class="text-slate-500">Status Surat</span><br>
                                @include('partials.surat-status-badge', [
                                    'status' => $surat->status
                                ])
                            </div>

                        </div>

                        {{-- ================= ALUR VERIFIKASI ================= --}}
                        <div class="bg-white rounded-xl border shadow-sm p-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                                Alur Verifikasi
                            </h3>

                            <ol class="space-y-4">
                                @foreach($alurSteps as $step)
                                    @php
                                        [$dot, $label] = match($step['state']) {
                                            'done'      => ['bg-emerald-500', 'Selesai'],
                                            'active'    => ['bg-blue-500 animate-pulse', 'Sedang Diproses'],
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

                    </div>
                </div>

            </main>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>
</x-app-layout>
