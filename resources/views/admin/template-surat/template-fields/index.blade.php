<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen bg-slate-50">
        <div class="flex min-h-screen">

            <x-layouts.sidebar />

            <div class="flex-1 flex flex-col transition-all duration-300"
                 :class="{
                    'lg:ml-72': !sidebarCollapsed,
                    'lg:ml-20': sidebarCollapsed
                 }">

                <x-layouts.topbar />

                <main class="p-6 space-y-6 max-w-6xl">

                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold">
                            Field Template: {{ $template->nama_template }}
                        </h1>

                        <a href="{{ route('admin.template-surat.index') }}"
                           class="px-4 py-2 bg-slate-200 rounded-lg text-sm">
                            ← Kembali
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 text-green-700 px-4 py-2 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- FORM TAMBAH FIELD --}}
                    <form method="POST"
                          action="{{ route('admin.template-surat.template-fields.store', $template->id) }}"
                          class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-white p-4 border rounded-xl">
                        @csrf

                        <input name="field_key" placeholder="field_key"
                               class="border rounded px-3 py-2" required>

                        <input name="label" placeholder="Label"
                               class="border rounded px-3 py-2" required>

                        <select name="type" class="border rounded px-3 py-2">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="date">Date</option>
                            <option value="number">Number</option>
                        </select>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="required" value="1">
                            Wajib
                        </label>

                        <button class="bg-indigo-600 text-white rounded px-4 py-2">
                            Tambah
                        </button>
                    </form>

                    {{-- LIST FIELD --}}
                    <div class="bg-white border rounded-xl overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="px-3 py-2">Urutan</th>
                                    <th>Field Key</th>
                                    <th>Label</th>
                                    <th>Tipe</th>
                                    <th>Wajib</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($fields as $f)
                                <tr>
                                    <td class="px-3 py-2">{{ $f->order }}</td>
                                    <td><code>{{ $f->field_key }}</code></td>
                                    <td>{{ $f->label }}</td>
                                    <td>{{ $f->type }}</td>
                                    <td>{{ $f->required ? 'Ya' : 'Tidak' }}</td>
                                    <td>
                                        <form method="POST"
                                              action="{{ route('admin.template-surat.template-fields.destroy', [$template->id, $f->id]) }}"
                                              onsubmit="return confirm('Hapus field ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
