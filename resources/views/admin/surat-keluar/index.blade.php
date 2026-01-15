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
                <x-layouts.topbar />

                <main class="flex-1 bg-slate-50 p-4 sm:p-6">
                    <div class="space-y-6">

                        {{-- HEADER --}}
                        <div class="flex items-center justify-between">
                            <h1 class="text-xl font-semibold text-slate-800">
                                Monitoring Surat Keluar
                            </h1>
                        </div>

                        {{-- TABLE --}}
                        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-slate-50 border-b text-slate-600">
                                        <tr>
                                            <th class="px-4 py-3 text-left w-12">No</th>
                                            <th class="px-4 py-3 text-left">Nomor Surat</th>
                                            <th class="px-4 py-3 text-left">Perihal</th>
                                            <th class="px-4 py-3 text-left">Pembuat</th>
                                            <th class="px-4 py-3 text-left">Template</th>
                                            <th class="px-4 py-3 text-left w-28">Status</th>
                                            <th class="px-4 py-3 text-left w-28">Tanggal</th>
                                            <th class="px-4 py-3 text-center w-20">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y">
                                        @forelse ($suratKeluar as $index => $surat)

                                            @php
                                                $statusBadge = match($surat->status) {
                                                    'draft'        => 'bg-slate-100 text-slate-700',
                                                    'diajukan'     => 'bg-blue-100 text-blue-700',
                                                    'diproses'     => 'bg-indigo-100 text-indigo-700',
                                                    'ditandatangani' => 'bg-green-100 text-green-700',
                                                    'final'        => 'bg-emerald-100 text-emerald-700',
                                                    'ditolak'      => 'bg-red-100 text-red-700',
                                                    default        => 'bg-slate-100 text-slate-600',
                                                };
                                            @endphp

                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="px-4 py-3 text-slate-500">
                                                    {{ $suratKeluar->firstItem() + $index }}
                                                </td>

                                                <td class="px-4 py-3 font-medium text-slate-700 whitespace-nowrap">
                                                    {{ $surat->nomor_surat ?? '—' }}
                                                </td>

                                                <td class="px-4 py-3 text-slate-700 max-w-xs truncate"
                                                    title="{{ $surat->perihal }}">
                                                    {{ $surat->perihal }}
                                                </td>

                                                <td class="px-4 py-3 text-slate-600">
                                                    {{ $surat->pembuat->name ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3 text-slate-600">
                                                    {{ $surat->template->nama_template ?? '-' }}
                                                </td>

                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $statusBadge }}">
                                                        {{ strtoupper($surat->status) }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                                    {{ $surat->tanggal_surat?->format('d M Y') }}
                                                </td>

                                                <td class="px-4 py-3 text-center">
                                                    <a href="{{ route('admin.surat-keluar.show', $surat) }}"
                                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                        Detail →
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                                                    Tidak ada surat keluar.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- PAGINATION --}}
                        <div class="pt-2">
                            {{ $suratKeluar->links() }}
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
