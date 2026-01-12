<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            <x-layouts.sidebar />

            <div class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                 x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 x-cloak></div>

            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                 }">

                <x-layouts.topbar />

                <main class="p-6 space-y-6">

                    {{-- HEADER --}}
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Surat Masuk
                        </h1>
                        <p class="text-sm text-slate-500">
                            Surat yang menunggu atau telah diverifikasi oleh Anda
                        </p>
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 w-12">#</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Unit Asal</th>
                                    <th class="px-4 py-3">Jenis Surat</th>
                                    <th class="px-4 py-3">Perihal</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center w-32">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse ($suratMasuk as $i => $surat)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-slate-500">
                                            {{ $i + 1 }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ optional($surat->tanggal_surat)->format('d M Y') }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $surat->unitAsal?->nama_unit ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3">
                                            {{ $surat->template->nama_template }}
                                        </td>

                                        <td class="px-4 py-3 max-w-[260px] truncate">
                                            {{ $surat->perihal }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            @include('partials.surat-status-badge', [
                                                'status' => $surat->status
                                            ])
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('sekretaris-direktur.surat-masuk.show', $surat->id) }}"
                                               class="px-3 py-1 text-xs rounded-lg
                                                      {{ $surat->status === 'final'
                                                          ? 'bg-slate-100 text-slate-700'
                                                          : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                                {{ $surat->status === 'final' ? 'Detail' : 'Verifikasi' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="px-6 py-10 text-center text-slate-500 italic">
                                            Tidak ada surat masuk
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
