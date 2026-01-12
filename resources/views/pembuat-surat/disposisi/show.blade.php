<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

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
                <x-layouts.topbar />

                <main class="flex-1 p-6 space-y-6">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-800">
                                Detail Disposisi
                            </h1>
                            <p class="text-sm text-slate-500">
                                Tinjau dan tindak lanjuti disposisi surat
                            </p>
                        </div>

                        <a
                            href="{{ route('pembuat-surat.disposisi.index') }}"
                            class="px-4 py-2 rounded-lg border text-slate-600 hover:bg-slate-100"
                        >
                            ← Kembali
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {{-- PREVIEW SURAT --}}
                        <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm">
                            <div class="p-6 overflow-auto max-h-[75vh]">
                                <div class="mx-auto bg-gray-100 p-6 rounded-lg" style="max-width: 21cm">
                                    <div class="mx-auto bg-white shadow" style="min-height: 29.7cm">
                                        @include('partials.surat-preview', [
                                            'template' => $surat->template,
                                            'previewHtml' => $previewHtml,
                                            'surat' => $surat
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- INFO & ACTION --}}
                        <div class="space-y-6">

                            {{-- INFO --}}
                            <div class="bg-white rounded-xl border shadow-sm p-5 text-sm space-y-4">

                                <div>
                                    <span class="text-slate-500">Perihal</span>
                                    <p class="font-medium text-slate-800">
                                        {{ $surat->data_json['perihal'] ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500">Asal Surat</span>
                                    <p class="font-medium text-slate-800">
                                        {{ $surat->unitAsal->nama_unit ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500">Tanggal Disposisi</span>
                                    <p class="font-medium text-slate-800">
                                        {{ $disposisi->created_at->format('d M Y') }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500">Instruksi</span>
                                    <p class="font-medium text-slate-800">
                                        {{ $disposisi->instruksi ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <span class="text-slate-500">Status</span>
                                    <span class="inline-block px-2 py-1 text-xs rounded
                                        {{ $disposisi->status === 'pending'
                                            ? 'bg-yellow-100 text-yellow-700'
                                            : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($disposisi->status) }}
                                    </span>
                                </div>

                            </div>

                            {{-- ACTION --}}
                            <div class="bg-white rounded-xl border shadow-sm p-5 space-y-3">

                                @if($disposisi->status === 'pending')
                                    <form
                                        method="POST"
                                        action="{{ route('pembuat-surat.disposisi.tindaklanjuti', $surat) }}"
                                        onsubmit="return confirm('Tindak lanjuti disposisi ini?')"
                                    >
                                        @csrf
                                        <button
                                            class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700"
                                        >
                                            Tindak Lanjuti Disposisi
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center text-sm text-slate-500">
                                        Disposisi telah diselesaikan.
                                    </div>
                                @endif

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
