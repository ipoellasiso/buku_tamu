<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e2a78">
    <title>{{ $set->nama_aplikasi }} — {{ $set->nama_pendek }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#1e2a78] to-blue-700 flex flex-col items-center justify-center p-5 antialiased">

<div class="text-center text-white mb-6">
    @if($set->logo_url)
        <img src="{{ $set->logo_url }}" alt="Logo" class="w-16 h-16 mx-auto rounded-2xl bg-white p-2 object-contain mb-3">
    @else
        <div class="w-16 h-16 mx-auto rounded-2xl bg-white/15 grid place-items-center text-3xl mb-3">📖</div>
    @endif
    <h1 class="text-2xl font-extrabold">{{ $set->nama_aplikasi }}</h1>
    <p class="text-blue-200 text-sm">{{ $set->nama_pendek }}</p>
</div>

<div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl p-6">
    <h2 class="font-bold text-lg text-slate-800">Selamat datang 👋</h2>
    <p class="text-sm text-slate-500 mb-5">Pilih sesuai keperluan Anda.</p>

    <a href="{{ route('walkin.create') }}"
       class="block rounded-2xl border-2 border-[#3f5bd9] bg-blue-50 p-4 active:scale-[.98] transition">
        <p class="font-bold text-[#1e2a78]">🏢 Saya sudah di kantor</p>
        <p class="text-xs text-slate-500 mt-1">Isi data tamu langsung tanpa akun. Resepsionis akan memprosesnya.</p>
    </a>

    <a href="{{ route('register') }}"
       class="block rounded-2xl border border-slate-200 p-4 mt-3 active:bg-slate-50">
        <p class="font-bold text-slate-700">🗓 Ajukan kunjungan terjadwal</p>
        <p class="text-xs text-slate-500 mt-1">Buat akun, pilih pegawai dan waktu. Setelah disetujui Anda mendapat QR.</p>
    </a>

    <a href="{{ route('login') }}" class="block text-center mt-4 text-sm text-[#3f5bd9] font-medium">
        Sudah punya akun? Masuk
    </a>
</div>

<p class="text-blue-200 text-xs mt-6">© {{ now()->year }} {{ $set->nama_pendek }}</p>
</body>
</html>