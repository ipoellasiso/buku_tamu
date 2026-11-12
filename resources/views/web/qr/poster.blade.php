<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Poster QR Buku Tamu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 0; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .qr svg { width: 100%; height: auto; display: block; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .sheet { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-200">

<div class="no-print fixed top-4 right-4 flex gap-2 z-10">
    <button onclick="window.print()" class="px-4 py-2 bg-blue-700 text-white rounded-lg text-sm shadow">🖨 Cetak</button>
    <a href="{{ route('qr.index') }}" class="px-4 py-2 bg-white rounded-lg text-sm shadow">Kembali</a>
</div>

<div class="sheet mx-auto my-6 bg-white shadow-xl w-[210mm] h-[297mm] flex flex-col items-center text-center overflow-hidden">

    <div class="w-full bg-[#1e2a78] text-white py-10 px-10">
        @if($set->logo_url)
            <img src="{{ $set->logo_url }}" alt="Logo" class="h-20 mx-auto mb-4 object-contain bg-white rounded-xl p-2">
        @endif
        <p class="text-sm tracking-[.3em] text-blue-200">{{ mb_strtoupper($set->nama_pendek) }}</p>
        <h1 class="text-5xl font-extrabold mt-3">{{ mb_strtoupper($set->nama_aplikasi) }}</h1>
    </div>

    <p class="mt-10 text-2xl font-semibold text-slate-700">Scan untuk mengisi data kunjungan Anda</p>

    <div class="qr w-[110mm] mt-8 p-5 border-[6px] border-[#1e2a78] rounded-3xl">
        {!! $qr !!}
    </div>

    <p class="mt-5 text-sm text-slate-500 break-all px-16">{{ $link }}</p>

    <div class="mt-auto w-full grid grid-cols-3 gap-6 px-14 pb-14 pt-10 text-left">
        @foreach([
            ['Scan QR',          'Arahkan kamera HP ke QR di poster ini.'],
            ['Pilih Jalur',      'Sudah di kantor? Isi data tamu langsung. Ingin menjadwalkan? Ajukan kunjungan.'],
            ['Lapor Resepsionis','Tunjukkan layar konfirmasi Anda ke resepsionis.'],
        ] as [$judul, $ket])
        <div>
            <span class="w-9 h-9 rounded-full bg-[#1e2a78] text-white grid place-items-center font-bold mb-2">{{ $loop->iteration }}</span>
            <p class="font-semibold text-slate-800">{{ $judul }}</p>
            <p class="text-sm text-slate-500 mt-1">{{ $ket }}</p>
        </div>
        @endforeach
    </div>
</div>

</body>
</html>