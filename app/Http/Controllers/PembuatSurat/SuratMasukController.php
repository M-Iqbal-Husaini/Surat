<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratVerifikasi;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;


class SuratMasukController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_if(!$user->jabatan_id || !$user->unit_id, 403);

        $suratMasuk = Surat::with([
                'template',
                'unitAsal',
                'verifikasi',
            ])
            // ⬇️ INI KUNCI UTAMA
            ->where('unit_tujuan_id', $user->unit_id)

            // ⬇️ Surat bukan buatan sendiri
            ->where('pembuat_id', '!=', $user->id)

            // ⬇️ Status yang masih relevan untuk ditampilkan
            ->whereIn('status', [
                'diajukan',
                'diproses',
                'menunggu',
                'disposisi',
                'selesai',
                'final',
            ])
            ->orderByDesc('tanggal_surat')
            ->get()
            ->map(function (Surat $surat) use ($user) {

                $stepSaya = $surat->verifikasi
                    ->firstWhere('jabatan_id', $user->jabatan_id);

                if ($surat->status === 'final') {
                    $label = 'Final';
                    $badge = 'bg-green-100 text-green-700';
                } elseif ($surat->status === 'ditolak') {
                    $label = 'Ditolak';
                    $badge = 'bg-red-100 text-red-700';
                } elseif ($surat->status === 'disposisi') {
                    $label = 'Disposisi';
                    $badge = 'bg-purple-100 text-purple-700';
                } elseif ($surat->status === 'diajukan') {
                    $label = 'Diajukan';
                    $badge = 'bg-blue-100 text-blue-700';
                } else {
                    $label = 'Diproses';
                    $badge = 'bg-yellow-100 text-yellow-800';
                }

                return [
                    'model'       => $surat,
                    'statusLabel' => $label,
                    'statusBadge' => $badge,
                    'needAction'  => in_array($surat->status, [
                        'diajukan',
                        'diproses',
                        'disposisi',
                    ]),
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
                )->with([
            'unitTujuan' => Unit::where('jenis_unit', 'unit')
                ->where('id', '!=', $user->unit_id)
                ->orderBy('nama_unit')
                ->get(),
        ]);
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
            'unit_tujuan_id'    => 'required|exists:units,id',
            'catatan_disposisi' => 'required|string|max:1000',
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
