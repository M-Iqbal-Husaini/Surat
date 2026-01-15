<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            <x-layouts.sidebar />

            <div
                class="flex-1 flex flex-col transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                }"
            >
                <x-layouts.topbar />

                <main class="flex-1 p-4 sm:p-6 space-y-6">

                    {{-- HEADER --}}
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">
                            Disposisi Masuk
                        </h1>
                        <p class="text-sm text-slate-500">
                            Daftar disposisi yang perlu ditindaklanjuti
                        </p>
                    </div>

                    {{-- TABLE --}}
                    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 text-slate-600">
                                <tr>
                                    <th class="p-3 text-left">Tanggal</th>
                                    <th class="p-3 text-left">Asal Surat</th>
                                    <th class="p-3 text-left">Catatan</th>
                                    <th class="p-3 text-center">Status</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disposisi as $item)
                                    <tr class="border-t hover:bg-slate-50">
                                        <td class="p-3">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3">
                                            {{ $item->surat->unitAsal->nama_unit ?? '-' }}
                                        </td>
                                        <td class="p-3">
                                            {{ $item->surat->perihal}}
                                        </td>
                                        <td class="p-3 text-center">
                                            @php
                                                $statusMap = [
                                                    'pending' => ['bg-yellow-100 text-yellow-700', 'Pending'],
                                                    'selesai' => ['bg-emerald-100 text-emerald-700', 'Selesai'],
                                                ];
                                            @endphp

                                            <span class="inline-block px-2 py-1 text-xs rounded
                                                {{ $statusMap[$item->status][0] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ $statusMap[$item->status][1] ?? ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <a
                                                href="{{ route('pembuat-surat.disposisi.show', $item) }}"
                                                class="text-indigo-600 hover:underline font-medium"
                                            >
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-500">
                                            Tidak ada disposisi yang menunggu tindak lanjut.
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
