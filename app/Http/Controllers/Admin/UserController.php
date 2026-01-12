<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'unit', 'jabatan'])
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'units'    => Unit::orderBy('nama_unit')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
            'roles'    => Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'nip'        => 'nullable|unique:users,nip',
            'unit_id'    => 'nullable|exists:units,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'status'     => 'required|in:aktif,nonaktif',
            'role'       => 'required|exists:roles,name',
            'ttd'        => 'nullable|image|max:2048', // ✅ TTD
        ]);

        $ttdPath = null;
        if ($request->hasFile('ttd')) {
            $ttdPath = $request->file('ttd')->store('ttd', 'public');
        }

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'nip'               => $data['nip'] ?? null,
            'unit_id'           => $data['unit_id'] ?? null,
            'jabatan_id'        => $data['jabatan_id'] ?? null,
            'status'            => $data['status'],
            'ttd_path'          => $ttdPath,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }


    public function show(User $user)
    {
        $user->load(['roles', 'unit', 'jabatan']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user'     => $user->load('roles'),
            'units'    => Unit::orderBy('nama_unit')->get(),
            'jabatans' => Jabatan::orderBy('nama_jabatan')->get(),
            'roles'    => Role::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => 'required|string',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => 'nullable|min:6',
            'nip'        => 'nullable|unique:users,nip,' . $user->id,
            'unit_id'    => 'nullable|exists:units,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'status'     => 'required|in:aktif,nonaktif',
            'role'       => 'required|exists:roles,name',
            'ttd'        => 'nullable|image|max:2048',
        ]);

        $update = [
            'name'       => $data['name'],
            'email'      => $data['email'],
            'nip'        => $data['nip'] ?? null,
            'unit_id'    => $data['unit_id'] ?? null,
            'jabatan_id' => $data['jabatan_id'] ?? null,
            'status'     => $data['status'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('ttd')) {
            // hapus TTD lama jika ada
            if ($user->ttd_path && \Storage::disk('public')->exists($user->ttd_path)) {
                \Storage::disk('public')->delete($user->ttd_path);
            }

            $update['ttd_path'] = $request->file('ttd')->store('ttd', 'public');
        }

        $user->update($update);
        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui');
    }


    public function destroy(User $user)
    {
        // soft logic: jangan hapus, nonaktifkan
        $user->update(['status' => 'nonaktif']);

        return back()->with('success', 'User dinonaktifkan');
    }
}
