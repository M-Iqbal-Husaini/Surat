<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="min-h-screen bg-slate-50 p-6">
        <h1 class="text-xl font-semibold mb-4">Edit User</h1>

        <form method="POST"
            action="{{ route('admin.users.update', $user) }}"
            enctype="multipart/form-data"
            class="bg-white border rounded-xl p-6 space-y-4">
            @csrf
            @method('PUT')

            <x-user-form
                :user="$user"
                :units="$units"
                :jabatans="$jabatans"
                :roles="$roles"
            />

            <div>
                <label class="block text-sm font-medium">TTD</label>

                @if($user->ttd_path)
                    <div class="mb-2">
                        <img src="{{ $user->ttd_url }}"
                            class="h-24 border rounded">
                    </div>
                @endif

                <input type="file" name="ttd"
                    accept="image/*"
                    class="mt-1">

                <p class="text-xs text-gray-500 mt-1">
                    Upload untuk mengganti TTD
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded">
                    Batal
                </a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
