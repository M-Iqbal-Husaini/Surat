<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">
            <x-layouts.sidebar />

            <div
                class="fixed inset-0 bg-slate-900/40 z-30 lg:hidden"
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            <div
                class="flex-1 flex flex-col w-full min-h-screen transition-all duration-300"
                :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                }"
            >
                <x-layouts.topbar />

                <main class="flex-1 bg-slate-50 p-4 sm:p-6 space-y-6">

                    <h1 class="text-xl font-semibold text-gray-800">
                        Pilih Template Surat
                    </h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($templates as $template)
                            <div class="bg-white rounded-xl border shadow p-5 flex flex-col">
                                <h2 class="font-semibold text-gray-800">
                                    {{ $template->nama_template }}
                                </h2>

                                <p class="text-sm text-gray-600 mt-2">
                                    Jenis: {{ $template->jenisSurat->nama }}
                                </p>

                                <a href="{{ route('pembuat-surat.surat-keluar.create', ['template' => $template->id]) }}"
                                class="mt-4 inline-flex justify-center items-center
                                        px-4 py-2
                                        bg-blue-600 hover:bg-blue-700
                                        focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                        text-white text-sm font-medium
                                        rounded-lg
                                        transition">
                                    Gunakan Template
                                </a>
                            </div>
                        @endforeach
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
