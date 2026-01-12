<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="min-h-screen bg-slate-50 p-6">
        <h1 class="text-xl font-semibold mb-4">Tambah User</h1>

        <form method="POST"
            action="{{ route('admin.users.store') }}"
            enctype="multipart/form-data"
            class="bg-white border rounded-xl p-6 space-y-4">
            @csrf

            <x-user-form
                :units="$units"
                :jabatans="$jabatans"
                :roles="$roles"
            />

            <div>
                <label class="block text-sm font-medium">TTD (Opsional)</label>
                <input type="file" name="ttd"
                    accept="image/*"
                    class="mt-1">
                <p class="text-xs text-gray-500 mt-1">
                    PNG/JPG maksimal 2MB
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
