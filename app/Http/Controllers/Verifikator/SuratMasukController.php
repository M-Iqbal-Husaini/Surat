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

        /**
         * ===============================
         * WORKFLOW VERIFIKASI
         * ===============================
         * - pending           → perlu setujui / tolak
         * - selesai + perlu_ttd → perlu TTD
         */
        $workflow = Surat::query()
            ->whereHas('verifikasi', function ($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)
                ->whereColumn('urutan', 'surat.step_aktif')
                ->where(function ($q) {
                    $q->where('status', 'pending')
                        ->orWhere(function ($q) {
                            $q->where('status', 'selesai')
                            ->where('perlu_ttd', 1);
                        });
                });
            });

        /**
         * ===============================
         * DISPOSISI AKTIF KE SAYA
         * ===============================
         */
        $disposisi = Surat::query()
            ->whereHas('disposisiAktif', function ($q) use ($user) {
                $q->where('ke_jabatan_id', $user->jabatan_id)
                ->where('status', 'pending');
            });

        /**
         * ===============================
         * UNION + LOAD RELATION
         * ===============================
         */
        $suratMasuk = $workflow
            ->union($disposisi)
            ->with([
                'template',
                'unitAsal',
                'pembuat',
            ])
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
