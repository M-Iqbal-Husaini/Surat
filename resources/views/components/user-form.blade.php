@props([
    'user' => null,
    'units',
    'jabatans',
    'roles',
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <div>
        <label>Nama</label>
        <input name="name" class="w-full border rounded"
               value="{{ old('name', $user->name ?? '') }}" required>
    </div>

    <div>
        <label>Email</label>
        <input name="email" type="email" class="w-full border rounded"
               value="{{ old('email', $user->email ?? '') }}" required>
    </div>

    <div>
        <label>Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }}</label>
        <input name="password" type="password" class="w-full border rounded">
    </div>

    <div>
        <label>NIP</label>
        <input name="nip" class="w-full border rounded"
               value="{{ old('nip', $user->nip ?? '') }}">
    </div>

    <div>
        <label>Unit</label>
        <select name="unit_id" class="w-full border rounded">
            <option value="">-</option>
            @foreach($units as $u)
                <option value="{{ $u->id }}"
                    @selected(old('unit_id', $user->unit_id ?? '') == $u->id)>
                    {{ $u->nama_unit }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Jabatan</label>
        <select name="jabatan_id" class="w-full border rounded">
            <option value="">-</option>
            @foreach($jabatans as $j)
                <option value="{{ $j->id }}"
                    @selected(old('jabatan_id', $user->jabatan_id ?? '') == $j->id)>
                    {{ $j->nama_jabatan }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Role</label>
        <select name="role" class="w-full border rounded" required>
            @foreach($roles as $r)
                <option value="{{ $r->name }}"
                    @selected(
                        old('role', $user?->roles->first()?->name) === $r->name
                    )>
                    {{ $r->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Status</label>
        <select name="status" class="w-full border rounded">
            <option value="aktif"
                @selected(old('status', $user->status ?? '') === 'aktif')>
                Aktif
            </option>
            <option value="nonaktif"
                @selected(old('status', $user->status ?? '') === 'nonaktif')>
                Nonaktif
            </option>
        </select>
    </div>
</div>
