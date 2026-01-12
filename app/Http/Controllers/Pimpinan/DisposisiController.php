<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisposisiController extends Controller
{
    /**
     * LIST DISPOSISI MASUK
     */
    public function index()
    {
        $user = Auth::user();

        $disposisi = Surat::with(['template', 'unitAsal'])
            ->where('status', 'disposisi')
            ->where('pimpinan_tujuan_id', $user->id)
            ->latest()
            ->get();

        return view('pimpinan.disposisi.index', compact('disposisi'));
    }

    /**
     * DETAIL DISPOSISI
     */
    public function show(Surat $surat)
    {
        $user = Auth::user();

        abort_if(
            $surat->status !== 'disposisi' ||
            $surat->pimpinan_tujuan_id !== $user->id,
            403
        );

        $surat->load(['template.jenisSurat', 'unitAsal']);

        // render isi surat
        $html = $surat->template->body_html;
        foreach ($surat->data_json as $k => $v) {
            $html = str_replace(['{{'.$k.'}}', '{{ '.$k.' }}'], e($v), $html);
        }

        $previewHtml = $html;

        // tujuan disposisi (user)
        $usersTujuan = User::with('unit')
            ->where('status', 'aktif')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'admin'))
            ->where('id', '!=', auth()->id())
            ->orderBy('unit_id')
            ->orderBy('jabatan_id')
            ->get()
            ->groupBy(fn ($u) => $u->unit->nama_unit ?? 'Tanpa Unit');

        return view(
            'pimpinan.disposisi.show',
            compact('surat', 'previewHtml', 'usersTujuan')
        );
    }

    /**
     * KIRIM DISPOSISI KE TUJUAN BARU
     */
    public function kirim(Request $request, Surat $surat)
    {
        abort_if(
            $surat->status !== 'disposisi' ||
            $surat->pimpinan_tujuan_id !== auth()->id(),
            403
        );

        $request->validate([
            'user_tujuan_id' => 'required|exists:users,id',
        ]);

        $tujuan = User::findOrFail($request->user_tujuan_id);

        $surat->update([
            'pimpinan_tujuan_id' => $tujuan->id,
            'unit_tujuan_id'     => $tujuan->unit_id,
            'status'             => 'disposisi',
        ]);

        return redirect()
            ->route('pimpinan.disposisi.index')
            ->with('success', 'Disposisi berhasil diteruskan.');
    }

    public function setujui(Surat $surat)
    {
        abort_if(
            $surat->status !== 'disposisi' ||
            $surat->pimpinan_tujuan_id !== auth()->id(),
            403
        );

        $surat->update([
            'status' => 'final',
        ]);

        return redirect()
            ->route('pimpinan.surat-keluar.index')
            ->with('success', 'Surat berhasil disetujui.');
    }

}
