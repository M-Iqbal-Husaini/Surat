<?php

namespace App\Http\Controllers\SekretarisDirektur;

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
     * Surat yang SUDAH diproses Sekdir
     * =================================
     */
    public function index()
    {
        $user = Auth::user();
        $jabatan = $user->jabatan;

        abort_if(!$jabatan || $jabatan->is_pimpinan || !$user->unit_id, 403);

        $suratKeluar = Surat::with([
                'template',
                'unitAsal',
                'unitTujuan',
                'verifikasi',
            ])

            ->where('status', '!=', 'ditolak')

            // ⬇️ kunci unit sekkdir
            ->where('unit_tujuan_id', $user->unit_id)

            // ⬇️ pernah diproses oleh sekkdir
            ->whereHas('verifikasi', function ($q) use ($jabatan) {
                $q->where('jabatan_id', $jabatan->id);
            })

            // ⬇️ sudah keluar dari step sekkdir
            ->where(function ($q) use ($jabatan) {
                $q->whereDoesntHave('verifikasi', function ($v) use ($jabatan) {
                    $v->where('jabatan_id', $jabatan->id)
                    ->whereColumn('urutan', 'surat.step_aktif')
                    ->where('status', 'pending');
                });
            })

            ->orderByDesc('tanggal_surat')
            ->get();

        return view(
            'sekretaris-direktur.surat-keluar.index',
            compact('suratKeluar')
        );
    }


    /**
     * =================================
     * SHOW
     * READ ONLY (TANPA AKSI)
     * =================================
     */
    public function show(Surat $surat)
    {
        $user = Auth::user();
        $jabatan = $user->jabatan;

        abort_if($jabatan->is_pimpinan, 403);

        // Sekdir HARUS pernah terlibat
        $stepSaya = SuratVerifikasi::where('surat_id', $surat->id)
            ->where('jabatan_id', $jabatan->id)
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
            'sekretaris-direktur.surat-keluar.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps'
            )
        );
    }
}
