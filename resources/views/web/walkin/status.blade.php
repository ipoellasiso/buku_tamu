@php
    $kode = 'TL-' . str_pad($guest->id, 4, '0', STR_PAD_LEFT);
    $map = [
        'pending'   => ['⏳', 'Menunggu resepsionis', 'Data Anda sudah terkirim. Silakan menuju resepsionis dan tunjukkan layar ini.', 'bg-orange-50 text-orange-700'],
        'confirmed' => ['🏢', 'Silakan masuk',        'Anda sudah tercatat masuk. Selamat berkunjung!',                                   'bg-teal-50 text-teal-700'],
        'completed' => ['🏁', 'Kunjungan selesai',    'Terima kasih telah berkunjung ke ' . $set->nama_pendek . '.',                                 'bg-green-50 text-green-700'],
    ];
    [$ikon, $judul, $ket, $cls] = $map[$guest->status] ?? $map['pending'];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e2a78">
    @if($guest->status !== 'completed')
        <meta http-equiv="refresh" content="10">
    @endif
    <title>Status Tamu — {{ $set->nama_pendek }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#1e2a78] to-blue-700 flex items-center justify-center p-5 antialiased">

<div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl p-6 text-center">
    <div class="w-20 h-20 mx-auto rounded-full grid place-items-center text-4xl {{ $cls }}">{{ $ikon }}</div>
    <h1 class="text-xl font-bold text-slate-800 mt-4">{{ $judul }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $ket }}</p>

    <div class="mt-5 rounded-2xl bg-slate-50 py-3">
        <p class="text-[11px] uppercase tracking-wide text-slate-400">Kode Tamu</p>
        <p class="text-3xl font-extrabold text-[#1e2a78] tracking-widest">{{ $kode }}</p>
    </div>

    <dl class="mt-5 text-sm text-left divide-y">
        @foreach([
            ['Nama',        $guest->name],
            ['Instansi',    $guest->institution ?? '-'],
            ['Keperluan',   $guest->purpose],
            ['Yang Dituju', $guest->host ?? 'Ditentukan resepsionis'],
            ['Dikirim',     $guest->created_at->format('d/m/Y H:i')],
        ] as [$l, $v])
        <div class="flex justify-between gap-4 py-2">
            <dt class="text-slate-500 shrink-0">{{ $l }}</dt>
            <dd class="font-medium text-right">{{ $v }}</dd>
        </div>
        @endforeach
    </dl>

    @if($guest->status !== 'completed')
        <p class="text-[11px] text-slate-400 mt-4">Halaman ini diperbarui otomatis.</p>
    @endif

    <a href="{{ route('walkin.create') }}" class="block mt-4 text-sm text-[#3f5bd9] font-medium">
        Isi data tamu lain
    </a>
</div>
</body>
</html>