<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratVerifikasi;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;

class SuratMasukController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $suratVerifikasi = Surat::whereHas('verifikasi', function ($q) use ($user) {
            $q->where('jabatan_id', $user->jabatan_id)
            ->where('status', 'pending');
        });

        $suratDisposisi = Surat::whereHas('disposisiAktif', function ($q) use ($user) {
            $q->where('ke_jabatan_id', $user->jabatan_id)
            ->where('status', 'pending');
        });

        $suratMasuk = $suratVerifikasi
            ->union($suratDisposisi)
            ->with(['template', 'unitAsal', 'pembuat'])
            ->orderByDesc('tanggal_surat')
            ->get();

        $suratIds = SuratVerifikasi::query()
            ->where('jabatan_id', $user->jabatan_id)
            ->pluck('surat_id')
            ->unique();

        $suratList = Surat::with([
                'template',
                'unitAsal',
                'verifikasi',
            ])
            ->whereIn('id', $suratIds)
            ->orderByDesc('tanggal_surat')
            ->get();

        $suratMasuk = $suratList->map(function (Surat $surat) use ($user) {
            $stepSaya = $surat->verifikasi
                ->firstWhere('jabatan_id', $user->jabatan_id);

            if ($surat->status === 'final') {
                $label = 'Final';
                $badge = 'bg-green-100 text-green-700';
            } elseif ($surat->status === 'ditolak') {
                $label = 'Ditolak';
                $badge = 'bg-red-100 text-red-700';
            } elseif (
                $stepSaya &&
                $stepSaya->status === 'pending' &&
                $stepSaya->urutan === $surat->step_aktif
            ) {
                $label = 'Perlu Tindakan';
                $badge = 'bg-yellow-100 text-yellow-800';
            } elseif ($surat->status === 'disposisi') {
                $label = 'Disposisi';
                $badge = 'bg-purple-100 text-purple-700';
            } else {
                $label = 'Diproses';
                $badge = 'bg-blue-100 text-blue-700';
            }

            return [
                'model'       => $surat,
                'statusLabel' => $label,
                'statusBadge' => $badge,
                'needAction'  =>
                    $stepSaya &&
                    $stepSaya->status === 'pending' &&
                    $stepSaya->urutan === $surat->step_aktif,
            ];
        });

        return view(
            'pembuat-surat.surat-masuk.index',
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
            'disposisiAktif.dariJabatan',
            'disposisiAktif.keJabatan',
        ]);

        $stepSaya = $surat->verifikasi
            ->firstWhere('jabatan_id', $user->jabatan_id);

        $isArchive = in_array($surat->status, [
            'final', 'ditolak', 'direvisi', 'disposisi'
        ]);

        abort_if(!$stepSaya && !$isArchive, 403);

        $previewHtml = $surat->template->body_html;
        foreach ($surat->data_json ?? [] as $k => $v) {
            $previewHtml = str_replace(
                ['{{'.$k.'}}', '{{ '.$k.' }}'],
                nl2br(e($v)),
                $previewHtml
            );
        }

        $alurSteps = AlurVerifikasiPresenter::map($surat);

        $isMyTurn =
            $stepSaya &&
            $stepSaya->status === 'pending' &&
            $stepSaya->urutan === $surat->step_aktif;

        $canApprove =
            $isMyTurn &&
            !$stepSaya->perlu_ttd &&
            $surat->status !== 'disposisi';

        $canFinalize =
            $isMyTurn &&
            $stepSaya->perlu_ttd;

        $canDispose =
            $isMyTurn &&
            !$stepSaya->perlu_ttd &&
            $surat->status !== 'disposisi';

        $canRevise = $isMyTurn;
        $canReject = $isMyTurn;

        $canFollowUp =
            $surat->status === 'disposisi' &&
            $stepSaya &&
            $stepSaya->status === 'pending';

        $disposisiAktif = $surat->disposisiAktif;

        $jabatanTujuan = Jabatan::orderBy('nama_jabatan')->get();

        return view(
            'pembuat-surat.surat-masuk.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps',
                'stepSaya',
                'isArchive',
                'canApprove',
                'canFinalize',
                'canDispose',
                'canFollowUp',
                'canRevise',
                'canReject',
                'disposisiAktif',
                'jabatanTujuan'
            )
        );
    }

    public function terima(Surat $surat)
    {
        SuratWorkflowService::approve($surat, Auth::user());

        return back()->with('success', 'Surat berhasil diterima.');
    }

    public function finalisasi(Surat $surat)
    {
        SuratWorkflowService::sign($surat, Auth::user());

        return back()->with('success', 'Surat berhasil difinalisasi.');
    }

    public function tolak(Surat $surat, Request $request)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        SuratWorkflowService::reject(
            $surat,
            Auth::user(),
            $request->catatan
        );

        return redirect()
            ->route('pembuat-surat.surat-masuk.index')
            ->with('success', 'Surat berhasil ditolak.');
    }

    public function direvisi(Surat $surat, Request $request)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        SuratWorkflowService::revise(
            $surat,
            Auth::user(),
            $request->catatan
        );

        return redirect()
            ->route('pembuat-surat.surat-masuk.index')
            ->with('success', 'Surat dikembalikan untuk revisi.');
    }

    public function disposisi(Surat $surat, Request $request)
    {
        $request->validate([
            'ke_jabatan_id'     => 'required|exists:jabatans,id',
            'catatan_disposisi' => 'required|string|max:1000',
        ]);

        SuratWorkflowService::disposisi(
            $surat,
            Auth::user(),
            $request->ke_jabatan_id,
            $request->catatan_disposisi
        );

        return back()->with('success', 'Surat berhasil didisposisikan.');
    }

    public function tindaklanjuti(Surat $surat)
    {
        SuratWorkflowService::tindaklanjutiDisposisi(
            $surat,
            Auth::user()
        );

        return back()->with('success', 'Disposisi telah ditindaklanjuti.');
    }
}
