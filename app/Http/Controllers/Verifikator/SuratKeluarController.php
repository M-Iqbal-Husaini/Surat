<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $suratKeluar = Surat::query()
            ->whereHas('verifikasi', function ($q) use ($user) {
                $q->where('jabatan_id', $user->jabatan_id)
                ->whereIn('status', [
                    'pending',
                    'selesai',
                    'ditandatangani',
                    'ditolak',
                    'disposisi',
                ]);
            })
            ->whereIn('surat.status', [
                'diajukan',
                'diproses',
                'final',
                'ditolak',
                'disposisi',
            ])
            ->with(['template', 'unitAsal', 'pembuat'])
            ->orderByDesc('surat.updated_at')
            ->get();

        return view(
            'verifikator.surat-keluar.index',
            compact('suratKeluar')
        );
    }

    public function show(Surat $surat)
    {
        $user = Auth::user();

        // ================= AUTH =================
        $stepAktif = $surat->verifikasi()
            ->where('urutan', $surat->step_aktif)
            ->first();

        abort_if(!$stepAktif || $stepAktif->jabatan_id !== $user->jabatan_id, 403);

        // ================= LOAD =================
        $surat->load([
            'template.fields',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
        ]);

        // ================= PREVIEW =================
        $previewHtml = $surat->template->body_html;
        foreach ($surat->data_json ?? [] as $k => $v) {
            $previewHtml = str_replace(
                ['{{'.$k.'}}', '{{ '.$k.' }}'],
                nl2br(e($v)),
                $previewHtml
            );
        }

        // ================= ALUR =================
        $alurSteps = AlurVerifikasiPresenter::map($surat);

        // ================= FLAG AKSI =================
        $stepSaya = $stepAktif;
        $showApproveReject = false;
        $showTtd = false;

        if ($stepSaya->status === 'pending') {
            if ($stepSaya->perlu_ttd) {
                $showTtd = true;
            } else {
                $showApproveReject = true;
            }
        }

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
        $request->validate([
            'catatan' => 'required|string|min:5',
        ]);

        SuratWorkflowService::revise($surat, Auth::user(), $request->catatan);
        return back()->with('success', 'Surat dikembalikan untuk revisi.');
    }

    public function ttd(Surat $surat)
    {
        SuratWorkflowService::sign($surat, Auth::user());
        return back()->with('success', 'Surat berhasil ditandatangani.');
    }
}
