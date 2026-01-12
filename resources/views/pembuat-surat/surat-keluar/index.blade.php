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

            {{-- MAIN WRAPPER --}}
            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                 }">

                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- MAIN --}}
                <main class="p-6 space-y-6">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-semibold text-slate-800">
                                Surat Keluar
                            </h1>
                            <p class="text-sm text-slate-500">
                                Daftar surat yang Anda ajukan beserta status prosesnya
                            </p>
                        </div>

                        @can('create surat')
                            <a href="{{ route('pembuat-surat.surat-keluar.create') }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                + Buat Surat
                            </a>
                        @endcan
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 text-left w-12">#</th>
                                    <th class="px-4 py-3 text-left">Template</th>
                                    <th class="px-4 py-3 text-left">Perihal</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center w-40">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse ($suratKeluar as $i => $surat)
                                    <tr class="hover:bg-slate-50">

                                        {{-- NO --}}
                                        <td class="px-4 py-3 text-slate-500">
                                            {{ $i + 1 }}
                                        </td>

                                        {{-- TEMPLATE --}}
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-800">
                                                {{ $surat->template->nama_template }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $surat->template->kode_template }}
                                            </div>
                                        </td>

                                        {{-- JENIS --}}
                                        <td class="px-4 py-3">
                                            {{ $surat->perihal }}
                                        </td>

                                        {{-- TANGGAL --}}
                                        <td class="px-4 py-3">
                                            {{ optional($surat->tanggal_surat)->format('d M Y') ?? '-' }}
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="px-4 py-3 text-center">
                                            @php
                                            $statusMap = [
                                                'draft'     => ['Draft', 'bg-slate-100 text-slate-700'],
                                                'diajukan'  => ['Menunggu Verifikasi', 'bg-yellow-100 text-yellow-700'],
                                                'diproses'  => ['Sedang Diproses', 'bg-indigo-100 text-indigo-700'],
                                                'diterima'  => ['Menunggu TTD', 'bg-amber-100 text-amber-700'],
                                                'final'     => ['Selesai', 'bg-emerald-100 text-emerald-700'],
                                                'ditolak'   => ['Ditolak', 'bg-red-100 text-red-700'],
                                                'direvisi'  => ['Direvisi', 'bg-orange-100 text-orange-700'],
                                                'disposisi' => ['Disposisi', 'bg-purple-100 text-purple-700'],
                                            ];

                                            [$label, $color] = $statusMap[$surat->status]
                                                ?? [ucfirst($surat->status), 'bg-slate-100 text-slate-700'];
                                            @endphp

                                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                                {{ $label }}
                                            </span>
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="px-4 py-3">
                                            <div class="flex justify-center gap-2">

                                                {{-- DETAIL --}}
                                                <a href="{{ route('pembuat-surat.surat-keluar.show', $surat->id) }}"
                                                class="px-3 py-1 text-xs rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">
                                                    Detail
                                                </a>

                                                {{-- EDIT (UX ONLY, SECURITY DI CONTROLLER) --}}
                                                @if(in_array($surat->status, ['draft', 'direvisi']))
                                                    <a href="{{ route('pembuat-surat.surat-keluar.edit', $surat->id) }}"
                                                    class="px-3 py-1 text-xs rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                                        Edit
                                                    </a>
                                                @endif

                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 italic">
                                            Belum ada surat keluar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
