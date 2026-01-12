<div class="bg-white p-10 font-[Times_New_Roman] text-[12pt] leading-relaxed">

    {{-- KOP SURAT --}}
    @include('partials.kop-default')

    {{-- JUDUL & NOMOR --}}
    <div class="text-center my-6">
        <div class="font-bold uppercase underline text-[14pt]">
            {{ $template->nama_template }}
        </div>
        <div class="mt-1">
            Nomor :
            <span class="italic text-gray-600">
                ……../{{ $template->jenisSurat->kode }}/{{ now()->year }}
            </span>
        </div>
    </div>

    {{-- ISI SURAT --}}
    <div class="isi-surat">
        {!! $previewHtml !!}
    </div>

    {{-- TANDA TANGAN (DUMMY) --}}
    <div class="flex justify-end mt-16">
        <div class="text-left">
            Bengkalis, {{ now()->translatedFormat('d F Y') }}<br>
            <strong>{{ optional($template->penandatanganJabatan)->nama_jabatan ?? 'Direktur' }}</strong>
            <br><br><br>
            <strong>NAMA</strong><br>
            NIP. 000000000000
        </div>
    </div>

    {{-- KETERANGAN --}}
    <div class="mt-10 text-center text-xs text-gray-400 italic">
        *Ini adalah preview template surat, bukan surat resmi
    </div>
</div>

<style>
.isi-surat p {
    text-align: justify;
    text-indent: 1.25cm;
    margin-bottom: 12px;
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
</style>
