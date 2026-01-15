<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Presenters\AlurVerifikasiPresenter;
use App\Helpers\TemplateRenderer;

class SuratMasukController extends Controller
{
    public function index()
    {
        $suratMasuk = Surat::orderByDesc('id')->paginate(10);

        return view('admin.surat-masuk.index', compact('suratMasuk'));
    }

    public function show(Surat $surat)
    {
        $surat->load([
            'template',
            'unitAsal',
            'unitTujuan',
            'pembuat.jabatan',
            'verifikasi.jabatan',
        ]);

        // === ISI SURAT ===
        $previewHtml = TemplateRenderer::render($surat);

        // === ALUR VERIFIKASI ===
        $alurSteps = AlurVerifikasiPresenter::map($surat);

        $disposisiAktif = null;

        return view(
            'admin.surat-masuk.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps',
                'disposisiAktif'
            )
        );
    }
}
