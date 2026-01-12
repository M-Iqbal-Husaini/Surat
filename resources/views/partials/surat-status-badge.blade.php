@php
$statusMap = [
    'draft'     => ['Draft', 'bg-slate-100 text-slate-700'],
    'diajukan'  => ['Menunggu Verifikasi', 'bg-yellow-100 text-yellow-700'],
    'diproses'  => ['Sedang Diproses', 'bg-indigo-100 text-indigo-700'],
    'diterima'  => ['Menunggu TTD', 'bg-amber-100 text-amber-700'],
    'final'     => ['Selesai', 'bg-emerald-100 text-emerald-700'],
    'ditolak'   => ['Ditolak', 'bg-red-100 text-red-700'],
    'direvisi'  => ['Direvisi', 'bg-orange-100 text-orange-700'],
    'disposisi' => ['Disposisi', 'bg-purple-100 text-purple-700'],
];

[$label, $color] = $statusMap[$status]
    ?? [ucfirst($status), 'bg-slate-100 text-slate-700'];
@endphp

<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
    {{ $label }}
</span>
