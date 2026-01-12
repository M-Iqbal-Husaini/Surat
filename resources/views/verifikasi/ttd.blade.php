<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50">
        <div class="bg-white rounded-xl shadow border p-6 w-full max-w-md">

            <h1 class="text-lg font-semibold text-slate-800 mb-4">
                Verifikasi Tanda Tangan
            </h1>

            <div class="space-y-2 text-sm text-slate-700">
                <p>
                    <span class="text-slate-500">Nama Penandatangan</span><br>
                    <strong>{{ $surat->signedBy->name }}</strong>
                </p>

                <p>
                    <span class="text-slate-500">Jabatan</span><br>
                    <strong>{{ $surat->signedJabatan->nama_jabatan }}</strong>
                </p>

                <p>
                    <span class="text-slate-500">Tanggal TTD</span><br>
                    <strong>{{ $surat->signed_at->format('d M Y H:i') }}</strong>
                </p>

                <p>
                    <span class="text-slate-500">Nomor Surat</span><br>
                    <strong>{{ $surat->nomor_surat ?? '-' }}</strong>
                </p>
            </div>

            <div class="mt-6 p-3 bg-emerald-50 border border-emerald-200 rounded text-emerald-700 text-sm font-semibold">
                Tanda tangan elektronik VALID
            </div>

        </div>
    </div>
</x-app-layout>
