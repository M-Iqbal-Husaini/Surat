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
                                title="Surat Masuk"
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
                                title="Surat Keluar"
                                :value="$jumlahSuratKeluar"
                                color="bg-yellow-400"
                                icon="
                                    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='w-7 h-7'>
                                    <path d='M19.5 22.5a3 3 0 0 0 3-3v-8.174l-6.879 4.022 3.485 1.876a.75.75 0 1 1-.712 1.321l-5.683-3.06a1.5 1.5 0 0 0-1.422 0l-5.683 3.06a.75.75 0 0 1-.712-1.32l3.485-1.877L1.5 11.326V19.5a3 3 0 0 0 3 3h15Z' />
                                    <path d='M1.5 9.589v-.745a3 3 0 0 1 1.578-2.642l7.5-4.038a3 3 0 0 1 2.844 0l7.5 4.038A3 3 0 0 1 22.5 8.844v.745l-8.426 4.926-.652-.351a3 3 0 0 0-2.844 0l-.652.351L1.5 9.589Z' />
                                    </svg>
                                "
                            />
                        </div>

                        {{-- ANALYTICS (MASIH SAMA DENGAN VERSI KITA BARUSAN) --}}
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                            
                            {{-- Header Chart --}}
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h2 class="text-sm font-bold text-slate-800">
                                        Analytics Surat Tahun {{ $tahun }}
                                    </h2>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Monitoring volume surat masuk vs keluar
                                    </p>
                                </div>
                                {{-- Badge Tahun --}}
                                <span class="px-2 py-1 text-[10px] font-medium bg-slate-100 text-slate-600 rounded">
                                    {{ $tahun }}
                                </span>
                            </div>

                            @php
                                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                                // Cari nilai tertinggi untuk skala chart (supaya bar tidak mentok atau terlalu pendek)
                                // Jika data kosong semua, default max 1 agar tidak divide by zero
                                $maxData = max(array_merge($chartMasuk, $chartKeluar));
                                $maxValue = $maxData > 0 ? $maxData : 1;
                            @endphp

                            {{-- Chart Area Wrapper --}}
                            <div class="overflow-x-auto">
                                <div class="min-w-[600px] sm:min-w-full">
                                    
                                    {{-- BAR CHART CONTAINER --}}
                                    {{-- h-64 menetapkan tinggi area chart --}}
                                    <div class="flex items-end h-64 gap-3 sm:gap-4 border-b border-slate-100 pb-2">
                                        
                                        @foreach($months as $i => $bulan)
                                            @php
                                                // Hitung persentase tinggi bar
                                                $percentMasuk  = ($chartMasuk[$i] / $maxValue) * 100;
                                                $percentKeluar = ($chartKeluar[$i] / $maxValue) * 100;
                                            @endphp

                                            {{-- KOLOM PER BULAN --}}
                                            {{-- group: untuk trigger hover effect --}}
                                            <div class="flex-1 flex flex-col justify-end h-full group relative">
                                                
                                                {{-- Tooltip (Akan muncul saat mouse hover di kolom bulan ini) --}}
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-10 w-max">
                                                    <div class="bg-slate-800 text-white text-[10px] rounded py-1 px-2 shadow-lg">
                                                        <div class="font-semibold mb-1">{{ $bulan }}</div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                            <span>M: {{ $chartMasuk[$i] }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                                            <span>K: {{ $chartKeluar[$i] }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Area Bar (Side by Side) --}}
                                                <div class="flex items-end justify-center gap-[2px] w-full h-full">
                                                    
                                                    {{-- Bar Surat Masuk (Blue) --}}
                                                    <div class="w-full bg-blue-100 rounded-t-sm relative transition-all duration-500 ease-out"
                                                        style="height: {{ $percentMasuk }}%">
                                                        {{-- Inner Color (untuk efek visual lebih bagus) --}}
                                                        <div class="absolute bottom-0 w-full bg-blue-500 rounded-t-sm" style="height: 100%"></div>
                                                    </div>

                                                    {{-- Bar Surat Keluar (Yellow) --}}
                                                    <div class="w-full bg-yellow-100 rounded-t-sm relative transition-all duration-500 ease-out"
                                                        style="height: {{ $percentKeluar }}%">
                                                        <div class="absolute bottom-0 w-full bg-yellow-400 rounded-t-sm" style="height: 100%"></div>
                                                    </div>
                                                </div>

                                                {{-- Label Bulan --}}
                                                <div class="mt-2 text-center">
                                                    <span class="text-[10px] text-slate-400 font-medium group-hover:text-slate-600">
                                                        {{ $bulan }}
                                                    </span>
                                                </div>

                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Legend --}}
                                    <div class="flex justify-center gap-6 mt-6">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            <span class="text-xs text-slate-600">Surat Masuk</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                                            <span class="text-xs text-slate-600">Surat Keluar</span>
                                        </div>
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
