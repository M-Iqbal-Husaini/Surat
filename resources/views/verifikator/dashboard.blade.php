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
                <main class="flex-1 p-4 sm:p-6 space-y-6">

                    <h1 class="text-xl font-semibold text-slate-800">
                        Dashboard
                    </h1>

                    {{-- STAT CARDS --}}
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

                    {{-- ANALYTICS --}}
                    <div class="bg-white rounded-xl border shadow-sm p-6">

                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800">
                                    Analytics Surat Tahun {{ $tahun }}
                                </h2>
                                <p class="text-xs text-slate-500 mt-1">
                                    Perbandingan surat masuk dan surat keluar per bulan
                                </p>
                            </div>

                            <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-600">
                                {{ $tahun }}
                            </span>
                        </div>

                        {{-- LINE CHART --}}
                        <div class="relative h-[340px] w-[1250px] w-full overflow-hidden">
                            <canvas id="suratLineChart" class="w-full"></canvas>
                        </div>

                    </div>
                </main>
            </div>
        </div>
    </div>

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    const ctx = document.getElementById('suratLineChart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: '{{ $chartData[0]['label'] }}',
                    data: @json($chartData[0]['data']),
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                },
                {
                    label: '{{ $chartData[1]['label'] }}',
                    data: @json($chartData[1]['data']),
                    borderWidth: 2,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            layout: {
                padding: { left: 0, right: 0, top: 4, bottom: 0 }
            },

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        font: { size: 11 }
                    }
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: { size: 10 }
                    }
                },
                x: {
                    offset: false,
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12,
                        maxRotation: 0,
                        padding: 4,
                        font: { size: 10 }
                    },
                    grid: { display: false }
                }
            }
        }

    });
    </script>
</x-app-layout>
