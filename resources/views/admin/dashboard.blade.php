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

            {{-- MAIN WRAPPER --}}
            <div
                class="flex-1 flex flex-col transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                }"
            >
                <x-layouts.topbar />

                {{-- MAIN CONTENT --}}
                <main class="flex-1 p-4 sm:p-6 space-y-6">

                    <h1 class="text-xl font-semibold text-slate-800">
                        Dashboard
                    </h1>

                    {{-- STAT CARDS --}}
                    <div class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(240px,1fr))]">
                        <x-layouts.stat-card
                            title="Total Surat Masuk"
                            :value="$jumlahSuratMasuk"
                            color="bg-blue-500"
                        />

                        <x-layouts.stat-card
                            title="Total Surat Keluar"
                            :value="$jumlahSuratKeluar"
                            color="bg-yellow-400"
                        />

                        @isset($jumlahUser)
                            <x-layouts.stat-card
                                title="Total Akun User"
                                :value="$jumlahUser"
                                color="bg-red-400"
                            />
                        @endisset
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
