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
                x-transition.opacity
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            {{-- MAIN WRAPPER --}}
            <div
                class="flex-1 flex flex-col w-full min-h-screen transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,   // sidebar lebar
                    'lg:ml-20': sidebarCollapsed     // sidebar kecil
                }"
            >
                {{-- TOPBAR --}}
                <x-layouts.topbar />

                {{-- MAIN CONTENT --}}
                <main class="flex-1 bg-slate-50 p-4 sm:p-6">
                    <div class="w-full space-y-6">
                        <h1 class="text-xl font-semibold text-gray-800">
                            Dashboard
                        </h1>

                        {{-- STAT CARDS --}}
                        <div class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(240px,1fr))]">
                            {{-- Total Surat Masuk --}}
                            <x-layouts.stat-card
                                title="Total Surat Masuk"
                                :value="$jumlahSuratMasuk"
                                color="bg-blue-400"
                                icon="
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='w-7 h-7'>
                                    <path d='M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z' />
                                    <path d='M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z' />
                                    </svg>
                                "
                            />

                            {{-- Total Surat Keluar --}}
                            <x-layouts.stat-card
                                title="Total Surat Keluar"
                                :value="$jumlahSuratKeluar"
                                color="bg-yellow-400"
                                icon="
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='w-7 h-7'>
                                    <path d='M19.5 22.5a3 3 0 0 0 3-3v-8.174l-6.879 4.022 3.485 1.876a.75.75 0 1 1-.712 1.321l-5.683-3.06a1.5 1.5 0 0 0-1.422 0l-5.683 3.06a.75.75 0 0 1-.712-1.32l3.485-1.877L1.5 11.326V19.5a3 3 0 0 0 3 3h15Z' />
                                    <path d='M1.5 9.589v-.745a3 3 0 0 1 1.578-2.642l7.5-4.038a3 3 0 0 1 2.844 0l7.5 4.038A3 3 0 0 1 22.5 8.844v.745l-8.426 4.926-.652-.351a3 3 0 0 0-2.844 0l-.652.351L1.5 9.589Z' />
                                    </svg>
                                "
                            />

                            {{-- Total Akun User --}}
                            <x-layouts.stat-card
                                title="Total Akun User"
                                :value="$jumlahUser"
                                color="bg-red-400"
                                icon="
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='w-7 h-7'>
                                    <path fill-rule='evenodd' d='M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z' clip-rule='evenodd' />
                                    </svg>
                                "
                            />
                        </div>

                        {{-- ANALYTICS (MASIH SAMA DENGAN VERSI KITA BARUSAN) --}}
                        @php
                            $barsMasuk  = [160,380,300,250,280,260,360,180,220,310,200,340];
                            $barsKeluar = [120,260,200,190,210,190,240,130,180,230,160,260];
                            $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-800">
                                        Analytics Surat
                                    </h2>
                                    <p class="text-xs text-slate-400">
                                        Statistik surat 12 bulan terakhir (dummy chart)
                                    </p>
                                </div>

                                <div
                                    class="inline-flex rounded-full border border-slate-200 bg-slate-50 text-[11px] font-medium overflow-hidden">
                                    <button class="px-3 py-1.5 bg-white text-slate-800">
                                        12 months
                                    </button>
                                    <button class="px-3 py-1.5 text-slate-500 hover:bg-slate-100">
                                        30 days
                                    </button>
                                    <button class="px-3 py-1.5 text-slate-500 hover:bg-slate-100">
                                        7 days
                                    </button>
                                    <button class="px-3 py-1.5 text-slate-500 hover:bg-slate-100">
                                        24 hours
                                    </button>
                                </div>
                            </div>

                            <div class="mt-4 overflow-x-auto">
                                <div class="min-w-[640px]">
                                    <div class="flex items-end h-64 space-x-4">
                                        @foreach($months as $i => $bulan)
                                            <div class="flex-1 flex flex-col items-center">
                                                <div class="flex-1 flex items-end space-x-1 w-full">
                                                    <div class="flex-1 rounded-full bg-indigo-400"
                                                         style="height: {{ $barsMasuk[$i] / 4 }}%"></div>
                                                    <div class="flex-1 rounded-full bg-emerald-400 opacity-80"
                                                         style="height: {{ $barsKeluar[$i] / 4 }}%"></div>
                                                </div>
                                                <span class="text-[10px] text-slate-400 mt-2">{{ $bulan }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="flex items-center justify-end space-x-4 mt-4 text-[11px]">
                                        <span class="inline-flex items-center text-slate-500">
                                            <span class="h-2 w-2 rounded-full bg-indigo-500 mr-1.5"></span>
                                            Surat Masuk
                                        </span>
                                        <span class="inline-flex items-center text-slate-500">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Surat Keluar
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
