<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlurVerifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jabatan;

class JalurSuratController extends Controller
{
    /**
     * List semua alur
     */
    public function index()
    {
        $alurs = AlurVerifikasi::with('steps')
            ->orderBy('nama_alur')
            ->get();

        return view('admin.jalur-surat.index', compact('alurs'));
    }

    /**
     * Form create
     */
    public function create()
    {
        $jabatans = Jabatan::with('unit')
            ->orderBy('unit_id')
            ->orderBy('level')
            ->get();

        return view('admin.jalur-surat.create', compact('jabatans'));
    }

    /**
     * Simpan alur baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_alur' => 'required|string|max:100|unique:alur_verifikasi,kode_alur',
            'nama_alur' => 'required|string|max:255',

            'detail' => 'required|array|min:1',
            'detail.*.unit_scope' => 'required|in:asal,tujuan',
            'detail.*.jabatan_id' => 'required|exists:jabatans,id',
            'detail.*.perlu_ttd' => 'nullable|in:1',
        ]);

        DB::transaction(function () use ($validated) {

            $alur = AlurVerifikasi::create([
                'kode_alur' => $validated['kode_alur'],
                'nama_alur' => $validated['nama_alur'],
                'deskripsi' => null,
                'is_active' => true,
            ]);

            $totalStep = count($validated['detail']);

            foreach ($validated['detail'] as $i => $step) {

                $urutan = $i + 1;
                $fase   = $this->resolveFase($urutan, $totalStep);

                $alur->steps()->create([
                    'urutan'           => $i + 1,
                    'fase'             => $this->resolveFaseFromUnitScope($step['unit_scope']),
                    'unit_scope'       => $step['unit_scope'],
                    'jabatan_id'       => $step['jabatan_id'],
                    'perlu_ttd'        => !empty($step['perlu_ttd']),
                    'boleh_kembalikan' => false,
                ]);
            }
        });

        return redirect()
            ->route('admin.jalur-surat.index')
            ->with('success', 'Jalur surat berhasil dibuat');
    }


    /**
     * Form edit
     */
    public function edit(AlurVerifikasi $alur)
    {
        $alur->load('steps.jabatan');

        $jabatans = Jabatan::with('unit')
            ->orderBy('unit_id')
            ->orderBy('level')
            ->get();

        return view('admin.jalur-surat.edit', compact('alur', 'jabatans'));

    }

    /**
     * Update alur
     */
    public function update(Request $request, AlurVerifikasi $alur)
    {
        $validated = $request->validate([
            'nama_alur' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',

            'detail' => 'required|array|min:1',
            'detail.*.unit_scope' => 'required|in:asal,tujuan',
            'detail.*.jabatan_id' => 'required|exists:jabatans,id',
            'detail.*.perlu_ttd' => 'nullable|in:1',
        ]);

        DB::transaction(function () use ($validated, $alur) {

            $alur->update([
                'nama_alur' => $validated['nama_alur'],
                'is_active' => $validated['is_active'] ?? $alur->is_active,
            ]);

            $alur->steps()->delete();

            $totalStep = count($validated['detail']);

            foreach ($validated['detail'] as $i => $step) {

                $urutan = $i + 1;
                $fase   = $this->resolveFase($urutan, $totalStep);

                $alur->steps()->create([
                    'urutan'           => $i + 1,
                    'fase'             => $this->resolveFaseFromUnitScope($step['unit_scope']),
                    'unit_scope'       => $step['unit_scope'],
                    'jabatan_id'       => $step['jabatan_id'],
                    'perlu_ttd'        => !empty($step['perlu_ttd']),
                    'boleh_kembalikan' => false,
                ]);
            }
        });

        return redirect()
            ->route('admin.jalur-surat.index')
            ->with('success', 'Jalur surat berhasil diperbarui');
    }

    /**
     * Nonaktifkan alur (soft business delete)
     */
    public function destroy(AlurVerifikasi $alur)
    {
        $alur->update([
            'is_active' => false,
        ]);

        return back()->with('success', 'Jalur surat dinonaktifkan');
    }

    private function resolveFase(int $urutan, int $totalStep): int
    {
        $stepPerFase = (int) ceil($totalStep / 2);

        return $urutan <= $stepPerFase ? 1 : 2;
    }

    private function resolveFaseFromUnitScope(string $unitScope): int
    {
        return $unitScope === 'asal' ? 1 : 2;
    }


}
