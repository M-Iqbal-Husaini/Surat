<?php

namespace App\Http\Controllers\PembuatSurat;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\TemplateSurat;
use App\Models\AlurVerifikasi;
use App\Models\SuratVerifikasi;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Presenters\AlurVerifikasiPresenter;
use App\Services\SuratWorkflowService;

class SuratKeluarController extends Controller
{
    public function index()
    {
        $suratKeluar = Surat::with(['template', 'jenisSurat'])
            ->where('pembuat_id', Auth::id())
            ->orderByDesc('tanggal_surat')
            ->get();

        return view(
            'pembuat-surat.surat-keluar.index',
            compact('suratKeluar')
        );
    }

    public function create(Request $request)
    {
        if (!$request->filled('template')) {
            $templates = TemplateSurat::where('is_active', true)->get();

            return view(
                'pembuat-surat.surat-keluar.create',
                compact('templates')
            );
        }

        $template = TemplateSurat::with('fields')
            ->findOrFail($request->template);

        $alurs = AlurVerifikasi::with('steps')
            ->where('is_active', true)
            ->get();

        $units = Unit::orderBy('nama_unit')->get();

        return view(
            'pembuat-surat.surat-keluar.form',
            compact('template', 'alurs', 'units')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'template_id'    => 'required|exists:template_surat,id',
            'alur_id'        => 'required|exists:alur_verifikasi,id',
            'unit_tujuan_id' => 'required|exists:units,id',
            'perihal'        => 'required|string|max:255',
            'data'           => 'nullable|array',
        ]);

        $user = auth()->user();

        $template = TemplateSurat::with('fields')
            ->findOrFail($validated['template_id']);

        $alur = AlurVerifikasi::with('steps')
            ->findOrFail($validated['alur_id']);

        abort_if($alur->steps->isEmpty(), 422, 'Alur verifikasi belum memiliki step.');

        $unitTujuan = Unit::findOrFail($validated['unit_tujuan_id']);

        /**
         * =====================================================
         * RULE TTD PEMBUAT (SOURCE OF TRUTH)
         * =====================================================
         * - Internal unit                  -> false
         * - Antar unit ke DIREKTORAT       -> false
         * - Antar unit non-direktorat      -> true
         */
        $perluTtdPembuat =
            (int) $user->unit_id !== (int) $unitTujuan->id
            && $unitTujuan->jenis_unit !== 'direktorat';

        DB::transaction(function () use (
            $validated,
            $template,
            $alur,
            $user,
            $unitTujuan,
            $perluTtdPembuat
        ) {

            /* ===============================
            * CREATE SURAT
            * =============================== */
            $surat = Surat::create([
                'template_id'    => $template->id,
                'jenis_surat_id' => $template->jenis_surat_id,
                'alur_id'        => $alur->id,
                'pembuat_id'     => $user->id,
                'unit_asal_id'   => $user->unit_id,
                'unit_tujuan_id' => $unitTujuan->id,
                'perihal'        => $validated['perihal'],
                'data_json'      => $validated['data'] ?? [],
                'status'         => 'draft',
                'step_aktif'     => 1,
                'tanggal_surat'  => now(),
            ]);

            /* ===============================
            * CREATE STEP VERIFIKASI
            * =============================== */
            foreach ($alur->steps as $step) {

                $perluTtd = (bool) $step->perlu_ttd;

                // 🔥 STEP 1 (PEMBUAT) OVERRIDE TOTAL
                if ((int) $step->urutan === 1) {
                    $perluTtd = $perluTtdPembuat;
                }

                SuratVerifikasi::create([
                    'surat_id'   => $surat->id,
                    'jabatan_id' => $step->jabatan_id,
                    'unit_id'    => $step->unit_scope === 'asal'
                        ? $surat->unit_asal_id
                        : $surat->unit_tujuan_id,
                    'urutan'     => $step->urutan,
                    'status'     => $step->urutan === 1 ? 'pending' : 'draft',
                    'perlu_ttd'  => $perluTtd,
                ]);
            }
        });

        return redirect()
            ->route('pembuat-surat.surat-keluar.index')
            ->with('success', 'Surat berhasil dibuat.');
    }

    public function show(Surat $surat)
    {
        // ===============================
        // AUTH
        // ===============================
        abort_if($surat->pembuat_id !== auth()->id(), 403);

        // ===============================
        // LOAD RELATION
        // ===============================
        $surat->load([
            'template.fields',
            'unitAsal',
            'unitTujuan',
            'verifikasi.jabatan',
        ]);

        // ===============================
        // PREVIEW HTML
        // ===============================
        $previewHtml = $surat->template->body_html;

        foreach ($surat->data_json ?? [] as $key => $value) {
            $previewHtml = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                nl2br(e($value)),
                $previewHtml
            );
        }

        // ===============================
        // STEP AKTIF
        // ===============================
        $stepAktif = $surat->verifikasi
            ->firstWhere('urutan', $surat->step_aktif);

        abort_if(!$stepAktif, 500); // data rusak → HARUS ketahuan

        // ===============================
        // ALUR VERIFIKASI (SOURCE OF TRUTH)
        // ===============================
        $alurSteps = AlurVerifikasiPresenter::map($surat);

        // ===============================
        // FLAG UI (DEFAULT)
        // ===============================
        $canEdit        = false;
        $canAjukan      = false;
        $canTtdPembuat  = false;

        // ===============================
        // RULE UI PEMBUAT (HANYA STEP 1)
        // ===============================
        if (
            $surat->status === 'draft' &&
            (int) $stepAktif->urutan === 1
        ) {

            // boleh edit SELAMA draft
            $canEdit = true;

            /**
             * RULE UTAMA:
             * - perlu_ttd = true  → tampilkan tombol TTD
             * - perlu_ttd = false → tampilkan tombol AJUKAN
             *
             * Antar unit / internal TIDAK BOLEH ditebak di UI
             * Validasi final tetap di WorkflowService
             */
            if ($stepAktif->perlu_ttd) {
                $canTtdPembuat = true;
            } else {
                $canAjukan = true;
            }
        }

        return view(
            'pembuat-surat.surat-keluar.show',
            compact(
                'surat',
                'previewHtml',
                'alurSteps',
                'stepAktif',
                'canEdit',
                'canAjukan',
                'canTtdPembuat'
            )
        );
    }


    public function ajukan(Surat $surat)
    {
        SuratWorkflowService::submitByPembuat(
            $surat,
            auth()->user()
        );

        return back()->with('success', 'Surat berhasil diajukan.');
    }

    public function ttd(Surat $surat)
    {
        SuratWorkflowService::sign(
            $surat,
            auth()->user()
        );

        return back()->with('success', 'Surat berhasil ditandatangani dan diajukan.');
    }

    public function edit(Surat $surat)
    {
        abort_if(
            $surat->pembuat_id !== auth()->id()
            || !in_array($surat->status, ['draft', 'direvisi']),
            403
        );

        $editable = in_array($surat->status, ['draft', 'direvisi']);

        return view(
            'pembuat-surat.surat-keluar.edit',
            compact('surat', 'editable')
        );
    }

    public function update(Request $request, Surat $surat)
    {
        abort_if(
            $surat->pembuat_id !== auth()->id()
            || !in_array($surat->status, ['draft', 'direvisi']),
            403
        );

        $validated = $request->validate([
            'perihal' => 'required|string|max:255',
            'data'    => 'required|array',
        ]);

        DB::transaction(function () use ($surat, $validated) {
            $surat->update([
                'perihal'   => $validated['perihal'],
                'data_json' => $validated['data'],
                'status'    => 'draft',
            ]);

            SuratVerifikasi::where('surat_id', $surat->id)
                ->where('status', 'direvisi')
                ->update([
                    'status'   => 'pending',
                    'acted_at' => null,
                    'user_id'  => null,
                    'catatan'  => null,
                ]);

            $step = SuratVerifikasi::where('surat_id', $surat->id)
                ->where('status', 'pending')
                ->orderBy('urutan')
                ->first();

            if ($step) {
                $surat->update([
                    'step_aktif' => $step->urutan,
                ]);
            }
        });

        return redirect()
            ->route('pembuat-surat.surat-keluar.show', $surat)
            ->with('success', 'Perubahan disimpan dan surat siap diajukan kembali.');
    }

    private function isAntarUnit(Surat $surat): bool
    {
        return (int) $surat->unit_asal_id !== (int) $surat->unit_tujuan_id;
    }

    
}
