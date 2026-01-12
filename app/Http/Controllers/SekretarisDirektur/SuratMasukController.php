<?php

namespace App\Http\Controllers\SekretarisDirektur;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;

class SuratMasukController extends Controller
{
    /* =====================================================
     * INDEX – SURAT MENUNGGU AKSI SEKDIR
     * ===================================================== */
    public function index()
    {
        $user = auth()->user();

        // 1. Surat workflow (verifikasi)
        $workflow = Surat::query()
            ->whereHas('verifikasi', function ($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)
                ->where('status', 'pending');
            });

        // 2. Surat disposisi
        $disposisi = Surat::query()
            ->whereHas('disposisiAktif', function ($q) use ($user) {
                $q->where('ke_jabatan_id', $user->jabatan_id);
            });

        // 3. Gabung
        $suratMasuk = $workflow
            ->union($disposisi)
            ->with(['template', 'unitAsal', 'pembuat'])
            ->orderByDesc('tanggal_surat')
            ->get();

        return view('sekretaris-direktur.surat-masuk.index', compact('suratMasuk'));
    }


    /* =====================================================
     * SHOW – DETAIL SURAT MASUK SEKDIR
     * ===================================================== */
    public function show(Surat $surat)
    {
        $user = Auth::user();

        $surat->load([
            'template.jenisSurat',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
            'disposisiAktif.dariJabatan',
            'disposisiAktif.keJabatan',
        ]);

        $stepSaya = $surat->verifikasi
            ->firstWhere('jabatan_id', $user->jabatan_id);

        $isArchive = in_array($surat->status, [
            'final', 'ditolak', 'direvisi', 'disposisi'
        ]);

        abort_if(!$stepSaya && !$isArchive, 403);

        $previewHtml = $this->renderPreview($surat);

        $alurSteps = AlurVerifikasiPresenter::map($surat);

        $isMyTurn =
            $stepSaya &&
            $stepSaya->status === 'pending' &&
            $stepSaya->urutan === $surat->step_aktif;

        $canApprove = $isMyTurn;
        $canRevise  = $isMyTurn;
        $canReject  = $isMyTurn;
        $canDispose = $isMyTurn;

        return view(
            'sekretaris-direktur.surat-masuk.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps',
                'stepSaya',
                'canApprove',
                'canRevise',
                'canReject',
                'canDispose'
            )
        )->with([
            'unitTujuan' => Unit::where('jenis_unit', 'unit')
                ->where('id', '!=', $user->unit_id)
                ->orderBy('nama_unit')
                ->get(),
        ]);
    }

    /* =====================================================
     * SETUJUI – TERUSKAN KE PIMPINAN
     * ===================================================== */
    public function setujui(Surat $surat)
    {
        SuratWorkflowService::approve($surat, Auth::user());

        return redirect()
            ->route('sekretaris-direktur.surat-masuk.index')
            ->with('success', 'Surat diteruskan ke pimpinan.');
    }

    /* =====================================================
     * TOLAK
     * ===================================================== */
    public function tolak(Surat $surat)
    {
        SuratWorkflowService::reject($surat, Auth::user());

        return back()->with('success', 'Surat ditolak.');
    }

    /* =====================================================
     * REVISI KE PEMBUAT
     * ===================================================== */
    public function revisi(Request $request, Surat $surat)
    {
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);

        SuratWorkflowService::revise(
            $surat,
            Auth::user(),
            $request->catatan
        );

        return back()->with('success', 'Surat dikembalikan untuk revisi.');
    }

    /* =====================================================
     * DISPOSISI ADMINISTRATIF
     * ===================================================== */
    public function disposisi(Request $request, Surat $surat)
    {
        $request->validate([
            'unit_tujuan_id'    => 'required|exists:units,id',
            'catatan_disposisi' => 'required|string|min:5',
        ]);

        $jabatanTujuan = Jabatan::where('unit_id', $request->unit_tujuan_id)
            ->orderBy('level')
            ->firstOrFail();

        SuratWorkflowService::disposisi(
            $surat,
            Auth::user(),
            $jabatanTujuan->id,
            $request->catatan_disposisi
        );

        return back()->with('success', 'Disposisi berhasil dikirim.');
    }

    /* =====================================================
     * HELPER – RENDER PREVIEW HTML
     * ===================================================== */
    private function renderPreview(Surat $surat): string
    {
        $html = $surat->template->body_html;

        foreach ($surat->data_json ?? [] as $k => $v) {
            $html = str_replace(
                ['{{'.$k.'}}', '{{ '.$k.' }}'],
                nl2br(e($v)),
                $html
            );
        }

        return $html;
    }
}
