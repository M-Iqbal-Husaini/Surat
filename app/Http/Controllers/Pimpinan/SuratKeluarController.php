<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratVerifikasi;
use Illuminate\Support\Facades\Auth;
use App\Presenters\AlurVerifikasiPresenter;

class SuratKeluarController extends Controller
{
    /**
     * =================================
     * INDEX
     * Surat yang SUDAH diproses pimpinan
     * =================================
     */
    public function index()
    {
    $user = Auth::user();

    abort_if(!$user->jabatan || !$user->jabatan->is_pimpinan || !$user->unit_id, 403);

    $suratKeluar = Surat::with([
            'template',
            'unitAsal',
            'unitTujuan',
            'verifikasi',
        ])

        ->where('status', '!=', 'ditolak')

        // ⬇️ Kunci unit pimpinan
        ->where('unit_tujuan_id', $user->unit_id)

        // ⬇️ Pernah diverifikasi oleh pimpinan
        ->whereHas('verifikasi', function ($q) use ($user) {
            $q->where('jabatan_id', $user->jabatan_id);
        })

        // ⬇️ Sudah bukan di step pimpinan lagi
        ->where(function ($q) use ($user) {
            $q->whereDoesntHave('verifikasi', function ($v) use ($user) {
                $v->where('jabatan_id', $user->jabatan_id)
                  ->whereColumn('urutan', 'surat.step_aktif')
                  ->where('status', 'pending');
            });
        })

        ->orderByDesc('tanggal_surat')
        ->get();

    return view(
        'pimpinan.surat-keluar.index',
        compact('suratKeluar')
    );
    }


    /**
     * =================================
     * SHOW
     * READ ONLY – TANPA AKSI
     * =================================
     */
    public function show(Surat $surat)
    {
        $user = Auth::user();
        abort_if(!$user->jabatan->is_pimpinan, 403);

        // pimpinan HARUS pernah terlibat
        $stepSaya = SuratVerifikasi::where('surat_id', $surat->id)
            ->where('jabatan_id', $user->jabatan_id)
            ->first();

        abort_if(!$stepSaya, 403);

        $surat->load([
            'template.jenisSurat',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
        ]);

        /* ===============================
         * PREVIEW HTML
         * =============================== */
        $previewHtml = $surat->template->body_html;

        foreach ($surat->data_json ?? [] as $key => $value) {
            $previewHtml = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                nl2br(e($value)),
                $previewHtml
            );
        }

        /* ===============================
         * ALUR VERIFIKASI
         * =============================== */
        $alurSteps = AlurVerifikasiPresenter::map($surat);

        return view(
            'pimpinan.surat-keluar.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps'
            )
        );
    }
}
