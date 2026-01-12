<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class StrukturUnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount(['users', 'jabatans'])
            ->orderBy('nama_unit')
            ->get();

        return view('admin.struktur-unit.index', compact('units'));
    }

    public function create()
    {
        return view('admin.struktur-unit.create', [
            'parents' => Unit::orderBy('nama_unit')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_unit'  => 'required|string|unique:units,kode_unit',
            'nama_unit'  => 'required|string',
            'jenis_unit' => 'required|in:direktorat,jurusan,unit',
            'parent_id'  => 'nullable|exists:units,id',
        ]);

        Unit::create($data);

        return redirect()
            ->route('admin.struktur-unit.index')
            ->with('success', 'Unit berhasil ditambahkan');
    }

    public function show(Unit $unit)
    {
        $unit->load(['parent', 'children', 'users', 'jabatans']);

        return view('admin.struktur-unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.struktur-unit.edit', [
            'unit'    => $unit,
            'parents' => Unit::where('id', '!=', $unit->id)
                            ->orderBy('nama_unit')
                            ->get(),
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'kode_unit'  => 'required|string|unique:units,kode_unit,' . $unit->id,
            'nama_unit'  => 'required|string',
            'jenis_unit' => 'required|in:direktorat,jurusan,unit',
            'parent_id'  => 'nullable|exists:units,id',
        ]);

        $unit->update($data);

        return redirect()
            ->route('admin.struktur-unit.index')
            ->with('success', 'Unit berhasil diperbarui');
    }

    public function destroy(Unit $unit)
    {
        // SAFETY: jangan hapus jika masih dipakai
        if ($unit->users()->exists() || $unit->jabatans()->exists()) {
            return back()->with('error', 'Unit masih memiliki user atau jabatan');
        }

        $unit->delete();

        return back()->with('success', 'Unit berhasil dihapus');
    }
}
