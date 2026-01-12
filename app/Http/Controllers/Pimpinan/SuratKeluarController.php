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
        abort_if(!$user->jabatan->is_pimpinan, 403);

        $suratKeluar = Surat::with([
                'template',
                'unitAsal',
                'unitTujuan',
            ])
            ->whereHas('verifikasi', function ($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)
                  ->whereIn('status', [
                      'diterima',
                      'ditandatangani',
                      'ditolak',
                      'disposisi',
                      'selesai',
                  ]);
            })
            ->whereIn('status', [
                'final',
                'ditolak',
                'disposisi',
                'diterima',
            ])
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
