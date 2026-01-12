<div class="bg-white p-10 font-[Times_New_Roman] text-[12pt] leading-relaxed">

    {{-- ================= KOP SURAT ================= --}}
    @include('partials.kop-default')

    {{-- ================= JUDUL & NOMOR ================= --}}
    <div class="text-center my-6">
        <div class="font-bold uppercase underline text-[14pt]">
            {{ $template->nama_template }}
        </div>

        <div class="mt-1">
            Nomor :
            <span class="italic text-gray-600">
                @if(!empty($surat->nomor_surat))
                    {{ $surat->nomor_surat }}
                @else
                    — BELUM DIBERIKAN NOMOR —
                @endif
            </span>
        </div>
    </div>

    {{-- ================= ISI SURAT ================= --}}
    <div class="isi-surat">
        {!! $previewHtml !!}
    </div>

    {{-- ================= BLOK TTD ================= --}}
    @php
        $isSigned = !is_null($surat->signed_at);

        $signedUser = $isSigned ? $surat->signedBy : null;

        $nama = $signedUser?->name ?? 'Nama Penandatangan';

        $jabatan = $signedUser?->jabatan?->nama_jabatan ?? 'Jabatan';

        $tanggal = $isSigned
            ? $surat->signed_at->translatedFormat('d F Y')
            : now()->translatedFormat('d F Y');
    @endphp

    <div class="ttd-wrapper">
        <div class="ttd-box">

            {{-- TEMPAT & TANGGAL --}}
            <div class="ttd-text">
                Bengkalis, {{ $tanggal }}
            </div>

            {{-- JABATAN --}}
            <div class="ttd-jabatan">
                {{ $jabatan }}
            </div>

            {{-- BLOK TTD DIGITAL --}}
            @if($surat->canShowQr())
                    {!! $surat->qr_svg !!}
            @endif
            

            {{-- NAMA --}}
            <div class="ttd-nama">
                {{ $nama }}
            </div>

            {{-- KETERANGAN --}}
            @if ($isSigned)
                <div class="ttd-tanggal">
                    Ditandatangani secara elektronik
                </div>
            @endif

        </div>
    </div>

    {{-- ================= CATATAN ================= --}}
    <div class="mt-10 text-center text-xs text-gray-400 italic">
        *Surat ini sah dan memiliki kekuatan hukum setelah ditandatangani secara elektronik
    </div>

</div>

{{-- ================= STYLE ================= --}}
<style>
/* ================= ISI SURAT ================= */
.isi-surat p {
    text-align: justify;
    text-indent: 1.25cm;
    margin-bottom: 12px;
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: normal;
}

.isi-surat {
    max-width: 100%;
    overflow-x: hidden;
}

.isi-surat table {
    width: 100%;
    border-collapse: collapse;
    margin: 12px 0 16px 0;
    table-layout: fixed;
}

.isi-surat td {
    padding: 4px 2px;
    vertical-align: top;
    word-break: break-word;
}

.isi-surat td.label { width: 30%; }
.isi-surat td.separator { width: 2%; }
.isi-surat td.value { width: 68%; }

/* ================= BLOK TTD ================= */
.ttd-wrapper {
    width: 100%;
    margin-top: 40px;
}

.ttd-box {
    width: 240px;              /* lebar area TTD */
    margin-left: auto;
    margin-right: 80px;        /* geser ke tengah (atur sesuai selera) */
    text-align: center;
}

.ttd-text,
.ttd-jabatan,
.ttd-nama,
.ttd-tanggal {
    text-align: center;
    font-size: 12px;
}

.ttd-box svg {
    display: block;
    margin: 12px auto;
}

.ttd-jabatan {
    margin-bottom: 4px;
}

.ttd-image img {
    height: 120px;
    width: 120px;
    margin: 10px auto;
    display: block;
}

.ttd-placeholder {
    height: 120px;
    width: 120px;
    margin: 10px auto;
}

.ttd-nama {
    font-weight: bold;
    text-decoration: underline;
    margin-top: 6px;
}

.ttd-tanggal {
    margin-top: 4px;
    font-size: 11px;
    color: #555;
}
</style>
