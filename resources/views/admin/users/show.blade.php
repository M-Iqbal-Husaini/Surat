<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="min-h-screen bg-slate-50 p-6">
        <h1 class="text-xl font-semibold mb-6">Detail User</h1>

        <div class="bg-white border rounded-xl p-6 space-y-3">
            <div><b>Nama:</b> {{ $user->name }}</div>
            <div><b>Email:</b> {{ $user->email }}</div>
            <div><b>NIP:</b> {{ $user->nip ?? '-' }}</div>
            <div><b>Unit:</b> {{ $user->unit?->nama_unit ?? '-' }}</div>
            <div><b>Jabatan:</b> {{ $user->jabatan?->nama_jabatan ?? '-' }}</div>
            <div><b>Role:</b> {{ $user->roles->pluck('name')->implode(', ') }}</div>
            <div><b>Status:</b> {{ ucfirst($user->status) }}</div>
            <div>
                <b>TTD:</b>
                @if($user->ttd_path)
                    <img src="{{ $user->ttd_url }}" class="h-24 mt-2">
                @else
                    <span class="text-gray-500">Belum ada</span>
                @endif
            </div>

            <div class="pt-4">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded">
                    Edit
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
