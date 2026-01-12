<x-app-layout>
    <div class="p-6 space-y-4">
        <h1 class="text-xl font-semibold">Detail Unit</h1>

        <div class="bg-white border rounded-xl p-6 space-y-2">
            <div><b>Kode:</b> {{ $unit->kode_unit }}</div>
            <div><b>Nama:</b> {{ $unit->nama_unit }}</div>
            <div><b>Jenis:</b> {{ ucfirst($unit->jenis_unit) }}</div>
            <div><b>Parent:</b> {{ $unit->parent?->nama_unit ?? '-' }}</div>
            <div><b>Jumlah Jabatan:</b> {{ $unit->jabatans->count() }}</div>
            <div><b>Jumlah User:</b> {{ $unit->users->count() }}</div>

            @if($unit->children->count())
                <div class="pt-2">
                    <b>Sub Unit:</b>
                    <ul class="list-disc ml-5">
                        @foreach($unit->children as $child)
                            <li>{{ $child->nama_unit }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <a href="{{ route('admin.struktur-unit.edit', $unit) }}"
           class="px-4 py-2 bg-indigo-600 text-white rounded">
            Edit
        </a>
    </div>
</x-app-layout>
