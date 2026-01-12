<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold mb-4">Edit Unit</h1>

        <form method="POST"
              action="{{ route('admin.struktur-unit.update', $unit) }}"
              class="bg-white border rounded-xl p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label>Kode Unit</label>
                <input name="kode_unit" class="w-full border rounded"
                       value="{{ $unit->kode_unit }}" required>
            </div>

            <div>
                <label>Nama Unit</label>
                <input name="nama_unit" class="w-full border rounded"
                       value="{{ $unit->nama_unit }}" required>
            </div>

            <div>
                <label>Jenis Unit</label>
                <select name="jenis_unit" class="w-full border rounded">
                    <option value="direktorat" @selected($unit->jenis_unit === 'direktorat')>Direktorat</option>
                    <option value="jurusan" @selected($unit->jenis_unit === 'jurusan')>Jurusan</option>
                    <option value="unit" @selected($unit->jenis_unit === 'unit')>Unit</option>
                </select>
            </div>

            <div>
                <label>Parent Unit</label>
                <select name="parent_id" class="w-full border rounded">
                    <option value="">-</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}"
                            @selected($unit->parent_id == $p->id)>
                            {{ $p->nama_unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.struktur-unit.index') }}"
                   class="px-4 py-2 border rounded">Batal</a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</x-app-layout>
