<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;

class SuratMasukController extends Controller
{
    /* =====================================================
     * INDEX – SURAT MENUNGGU AKSI PIMPINAN
     * ===================================================== */
    public function index()
    {
        $user = Auth::user();

        abort_if(!$user->jabatan->is_pimpinan, 403);

        $suratMasuk = Surat::query()
            ->join('surat_verifikasi as sv', function ($join) use ($user) {
                $join->on('sv.surat_id', '=', 'surat.id')
                    ->on('sv.urutan', '=', 'surat.step_aktif')
                    ->where('sv.jabatan_id', $user->jabatan_id)
                    ->whereIn('sv.status', ['pending', 'diterima']);
            })
            ->select('surat.*')
            ->with(['template', 'unitAsal', 'pembuat'])
            ->orderByDesc('surat.tanggal_surat')
            ->get();

        return view(
            'pimpinan.surat-masuk.index',
            compact('suratMasuk')
        );
    }

    /* =====================================================
     * SHOW – DETAIL SURAT MASUK PIMPINAN
     * ===================================================== */
    public function show(Surat $surat)
    {
        $user = Auth::user();

        abort_if(!$user->jabatan->is_pimpinan, 403);

        $surat->load([
            'template.jenisSurat',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
        ]);

        $stepSaya = $surat->verifikasi
            ->firstWhere('jabatan_id', $user->jabatan_id);

        abort_if(!$stepSaya, 403);

        $isMyTurn =
            $stepSaya->status === 'pending'
            && $stepSaya->urutan === $surat->step_aktif;

        $previewHtml = $this->renderPreview($surat);

        $alurSteps = AlurVerifikasiPresenter::map($surat);

        $canApprove = $isMyTurn && !$stepSaya->perlu_ttd;
        $canTtd     = $isMyTurn && $stepSaya->perlu_ttd;

        return view(
            'pimpinan.surat-masuk.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps',
                'stepSaya',
                'canApprove',
                'canTtd'
            )
        );
    }

    /* =====================================================
     * SETUJUI (TANPA TTD)
     * ===================================================== */
    public function setujui(Surat $surat)
    {
        abort_if(!Auth::user()->jabatan->is_pimpinan, 403);

        SuratWorkflowService::approve($surat, Auth::user());

        return redirect()
            ->route('pimpinan.surat-masuk.index')
            ->with('success', 'Surat disetujui.');
    }

    /* =====================================================
     * TANDA TANGAN DIGITAL (FINAL)
     * ===================================================== */
    public function ttd(Surat $surat)
    {
        abort_if(!Auth::user()->jabatan->is_pimpinan, 403);

        SuratWorkflowService::sign($surat, Auth::user());

        return redirect()
            ->route('pimpinan.surat-keluar.index')
            ->with('success', 'Surat berhasil ditandatangani dan difinalisasi.');
    }

    /* =====================================================
     * TOLAK
     * ===================================================== */
    public function tolak(Surat $surat)
    {
        abort_if(!Auth::user()->jabatan->is_pimpinan, 403);

        SuratWorkflowService::reject($surat, Auth::user());

        return redirect()
            ->route('pimpinan.surat-masuk.index')
            ->with('success', 'Surat ditolak.');
    }

    /* =====================================================
     * HELPER – PREVIEW HTML
     * ===================================================== */
    private function renderPreview(Surat $surat): string
    {
        $html = $surat->template->body_html;

        foreach ($surat->data_json ?? [] as $key => $value) {
            $html = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                nl2br(e($value)),
                $html
            );
        }

        return $html;
    }
}
