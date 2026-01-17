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
     * INDEX – SURAT YANG PERLU AKSI PIMPINAN
     * ===================================================== */
    public function index()
    {
        $user = Auth::user();

        abort_if(
            !$user->jabatan ||
            !$user->jabatan->is_pimpinan ||
            !$user->unit_id,
            403
        );

        $suratMasuk = Surat::with([
                'template',
                'unitAsal',
                'pembuat',
                'verifikasi',
                'disposisi',
            ])
            // ⬇️ kunci surat ke unit pimpinan
            ->where('unit_tujuan_id', $user->unit_id)

            // ⬇️ pernah masuk ke pimpinan (verifikasi atau disposisi)
            ->where(function ($q) use ($user) {

                // pernah diverifikasi pimpinan
                $q->whereHas('verifikasi', function ($v) use ($user) {
                    $v->where('jabatan_id', $user->jabatan_id);
                })

                // atau pernah didisposisikan ke pimpinan
                ->orWhereHas('disposisi', function ($d) use ($user) {
                    $d->where('ke_jabatan_id', $user->jabatan_id);
                });
            })

            ->orderByDesc('tanggal_surat')
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

        // 1️⃣ HARD GUARD
        abort_if(!$user->jabatan->is_pimpinan, 403);

        // 2️⃣ LOAD RELASI WAJIB
        $surat->load([
            'template.jenisSurat',
            'unitAsal',
            'unitTujuan',
            'pembuat',
            'verifikasi.jabatan',
        ]);

        // 3️⃣ AMBIL STEP PIMPINAN
        $stepSaya = $surat->verifikasi
            ->firstWhere('jabatan_id', $user->jabatan_id);

        // boleh buka arsip, tapi tidak boleh aksi
        $isArchive = in_array($surat->status, [
            'final',
            'ditolak',
            'direvisi',
        ]);

        abort_if(!$stepSaya && !$isArchive, 403);

        // 4️⃣ CEK APAKAH GILIRAN SAYA
        $isMyTurn =
            $stepSaya &&
            $stepSaya->urutan === $surat->step_aktif;

        /**
         * =====================================================
         * LOGIKA AKSI (INI KUNCI UTAMA)
         * =====================================================
         */

        // 🔹 SETUJU / TOLAK
        $canApprove =
            $isMyTurn &&
            $stepSaya->status === 'pending';

        // 🔹 TTD (SETELAH APPROVE)
        $canTtd =
            $isMyTurn &&
            $stepSaya->perlu_ttd &&
            $stepSaya->status === 'selesai';

        // 5️⃣ PREVIEW HTML
        $previewHtml = $this->renderPreview($surat);

        // 6️⃣ ALUR VERIFIKASI
        $alurSteps = AlurVerifikasiPresenter::map($surat);

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
        $user = Auth::user();
        abort_if(!$user->jabatan || !$user->jabatan->is_pimpinan, 403);

        SuratWorkflowService::approve($surat, $user);

        return redirect()
            ->route('pimpinan.surat-masuk.index')
            ->with('success', 'Surat berhasil disetujui.');
    }

    /* =====================================================
     * TANDA TANGAN DIGITAL (FINAL)
     * ===================================================== */
    public function ttd(Surat $surat)
    {
        $user = Auth::user();
        abort_if(!$user->jabatan || !$user->jabatan->is_pimpinan, 403);

        SuratWorkflowService::sign($surat, $user);

        return redirect()
            ->route('pimpinan.surat-keluar.index')
            ->with('success', 'Surat berhasil ditandatangani dan difinalisasi.');
    }

    /* =====================================================
     * TOLAK
     * ===================================================== */
    public function tolak(Surat $surat)
    {
        $user = Auth::user();
        abort_if(!$user->jabatan || !$user->jabatan->is_pimpinan, 403);

        SuratWorkflowService::reject($surat, $user);

        return redirect()
            ->route('pimpinan.surat-masuk.index')
            ->with('success', 'Surat berhasil ditolak.');
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
