<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Presenters\AlurVerifikasiPresenter;
use App\Helpers\TemplateRenderer;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $suratKeluar = Surat::orderByDesc('id')->paginate(10);

        return view('admin.surat-keluar.index', compact('suratKeluar'));
    }

    public function show(Surat $surat)
    {
        $surat->load([
            'template',
            'pembuat.jabatan',
            'verifikasi.jabatan',
        ]);

        // === ISI SURAT (WAJIB ADA) ===
        $previewHtml = TemplateRenderer::render($surat);

        // === ALUR VERIFIKASI (SATU SUMBER KEBENARAN) ===
        $alurSteps = AlurVerifikasiPresenter::map($surat);

        return view(
            'admin.surat-keluar.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps'
            )
        );
    }
}
