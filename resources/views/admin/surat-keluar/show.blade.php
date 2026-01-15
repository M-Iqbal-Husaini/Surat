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
                            Detail Surat Keluar
                        </h1>
                        <p class="text-sm text-slate-500">
                            Monitoring dan audit surat keluar
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.surat-keluar.index') }}"
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
                                        'previewHtml' => $previewHtml ?? null,
                                        'surat'       => $surat
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">

                        <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3 text-sm">
                            <div>
                                <span class="text-slate-500">Pembuat Surat</span>
                                <p class="font-medium text-slate-800">
                                    {{ $surat->pembuat?->name ?? '-' }}
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

                            <div>
                                <span class="text-slate-500">Status Surat</span><br>
                                <span class="inline-block px-2 py-1 text-xs rounded bg-slate-100 text-slate-700">
                                    {{ strtoupper($surat->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border shadow-sm p-5">
                            <h3 class="text-sm font-semibold text-slate-700 mb-4">
                                Alur Verifikasi
                            </h3>

                            <ol class="space-y-4">
                                @foreach($alurSteps as $step)
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 w-3 h-3 rounded-full bg-slate-400"></span>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $step['jabatan'] }}
                                            </p>
                                            <p class="text-xs text-slate-500">
                                                {{ ucfirst($step['state']) }}
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

<style>[x-cloak]{display:none!important}</style>
</x-app-layout>
