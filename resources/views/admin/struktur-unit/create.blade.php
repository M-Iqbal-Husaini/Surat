<x-app-layout>
    <div class="p-6">
        <h1 class="text-xl font-semibold mb-4">Tambah Unit</h1>

        <form method="POST"
              action="{{ route('admin.struktur-unit.store') }}"
              class="bg-white border rounded-xl p-6 space-y-4">
            @csrf

            <div>
                <label>Kode Unit</label>
                <input name="kode_unit" class="w-full border rounded" required>
            </div>

            <div>
                <label>Nama Unit</label>
                <input name="nama_unit" class="w-full border rounded" required>
            </div>

            <div>
                <label>Jenis Unit</label>
                <select name="jenis_unit" class="w-full border rounded" required>
                    <option value="direktorat">Direktorat</option>
                    <option value="jurusan">Jurusan</option>
                    <option value="unit">Unit</option>
                </select>
            </div>

            <div>
                <label>Parent Unit</label>
                <select name="parent_id" class="w-full border rounded">
                    <option value="">-</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_unit }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.struktur-unit.index') }}"
                   class="px-4 py-2 border rounded">Batal</a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
