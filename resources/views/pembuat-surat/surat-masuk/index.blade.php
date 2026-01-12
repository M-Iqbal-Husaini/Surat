<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            <x-layouts.sidebar />

            <div
                class="flex-1 flex flex-col w-full min-h-screen transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                }"
            >
                <x-layouts.topbar />

                <main class="flex-1 bg-slate-50 p-4 sm:p-6 space-y-6">

                    {{-- HEADER --}}
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Surat Masuk
                        </h1>
                        <p class="text-sm text-slate-500">
                            Daftar surat yang masuk ke unit Anda
                        </p>
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white rounded-xl border shadow overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 text-slate-600">
                                <tr>
                                    <th class="p-3 text-left">Tanggal</th>
                                    <th class="p-3 text-left">Unit Asal</th>
                                    <th class="p-3 text-left">Jenis Surat</th>
                                    <th class="p-3 text-left">Perihal</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                            @forelse ($suratMasuk as $row)
                                @php($surat = $row['model'])

                                <tr class="hover:bg-slate-50">

                                    <td class="p-3 whitespace-nowrap">
                                        {{ $surat->tanggal_surat?->format('d M Y') }}
                                    </td>

                                    <td class="p-3">
                                        {{ $surat->unitAsal?->nama_unit ?? '-' }}
                                    </td>

                                    <td class="p-3">
                                        {{ $surat->template->nama_template }}
                                    </td>

                                    <td class="p-3 max-w-[260px] truncate">
                                        {{ $surat->perihal }}
                                    </td>

                                    <td class="p-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $row['statusBadge'] }}">
                                            {{ $row['statusLabel'] }}
                                        </span>
                                    </td>

                                    <td class="p-3 text-center space-x-2">
                                        <a
                                            href="{{ route('pembuat-surat.surat-masuk.show', $surat) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs rounded
                                                bg-blue-600 text-white hover:bg-blue-700">
                                            Lihat
                                        </a>

                                        @if($row['needAction'])
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs rounded
                                                    bg-yellow-50 text-yellow-700">
                                                Perlu Tindakan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-500">
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
