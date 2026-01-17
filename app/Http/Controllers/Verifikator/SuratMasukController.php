<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\SuratWorkflowService;
use App\Presenters\AlurVerifikasiPresenter;

class SuratMasukController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if(!$user->jabatan_id || !$user->unit_id, 403);

        $suratMasuk = Surat::with([
                'template',
                'unitAsal',
                'pembuat',
                'verifikasi',
                'disposisi',
            ])
            ->where('unit_tujuan_id', $user->unit_id)
            ->where(function ($q) use ($user) {

                // Pernah diverifikasi oleh saya
                $q->whereHas('verifikasi', function ($v) use ($user) {
                    $v->where('jabatan_id', $user->jabatan_id);
                })

                // ATAU pernah didisposisikan ke saya
                ->orWhereHas('disposisi', function ($d) use ($user) {
                    $d->where('ke_jabatan_id', $user->jabatan_id);
                });
            })
            ->orderByDesc('tanggal_surat')
            ->get();

        return view(
            'verifikator.surat-masuk.index',
            compact('suratMasuk')
        );
    }

    public function show(Surat $surat)
    {
        $user = Auth::user();

        $surat->load([
            'template.jenisSurat',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
        ]);

        $stepSaya = $surat->verifikasi
            ->firstWhere('jabatan_id', $user->jabatan_id);

        $isArchive = in_array($surat->status, ['final','ditolak','direvisi','disposisi']);

        abort_if(!$stepSaya && !$isArchive, 403);

        $previewHtml = $this->renderPreview($surat);

        $alurSteps = AlurVerifikasiPresenter::map($surat);

        $isMyTurn =
            $stepSaya &&
            $stepSaya->urutan === $surat->step_aktif;

        // 1️⃣ Verifikasi dulu
        $showApproveReject =
            $isMyTurn &&
            $stepSaya->status === 'pending';

        // 2️⃣ TTD hanya setelah disetujui
        $showTtd =
            $isMyTurn &&
            $stepSaya->perlu_ttd &&
            $stepSaya->status === 'selesai';


        return view('verifikator.surat-masuk.show', compact(
            'surat',
            'previewHtml',
            'alurSteps',
            'stepSaya',
            'showApproveReject',
            'showTtd'
        ));
    }

    public function setujui(Surat $surat)
    {
        SuratWorkflowService::approve($surat, Auth::user());
        return back()->with('success', 'Surat disetujui.');
    }

    public function tolak(Surat $surat)
    {
        SuratWorkflowService::reject($surat, Auth::user());
        return back()->with('success', 'Surat ditolak.');
    }

    public function revisi(Request $request, Surat $surat)
    {
        $request->validate(['catatan' => 'required|string|min:5']);

        SuratWorkflowService::revise($surat, Auth::user(), $request->catatan);
        return back()->with('success', 'Surat dikembalikan untuk revisi.');
    }

    public function ttd(Surat $surat)
    {
        SuratWorkflowService::sign($surat, Auth::user());
        return back()->with('success', 'Surat berhasil ditandatangani.');
    }

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
