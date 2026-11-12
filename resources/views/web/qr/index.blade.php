@extends('web.layouts.app')
@section('title', 'QR Situs')
@section('header', 'QR Code Situs')
@section('crumb', 'Pengaturan')
@section('subtitle', 'QR untuk tamu: scan, daftar, lalu isi data kunjungan')

@section('content')
@if($lokal)
<div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
    ⚠️ Alamat <b>{{ $base }}</b> hanya bisa dibuka di laptop ini, jadi QR tidak akan jalan di HP tamu.
    Isi alamat yang bisa diakses tamu (lihat panduan di samping), atau buka halaman ini lewat
    <code>http://IP-LAPTOP:8000/qr-situs</code>.
</div>
@endif

<div class="grid lg:grid-cols-5 gap-5">
    {{-- QR --}}
    <div class="card p-6 lg:col-span-2 text-center">
        <div class="w-64 max-w-full mx-auto p-3 border-2 border-[#1e2a78] rounded-2xl [&>svg]:w-full [&>svg]:h-auto bg-white">
            {!! $qr !!}
        </div>

        <p class="lbl mt-5">Isi QR</p>
        <input id="linkBox" readonly value="{{ $link }}" onclick="this.select()" class="inp text-center text-xs">

        <div class="grid grid-cols-2 gap-2 mt-4">
            <button type="button" onclick="salin()" class="btn-outline">📋 Salin Link</button>
            <a href="{{ route('qr.unduh') }}" class="btn-outline">⬇ Unduh QR</a>
        </div>
        <a href="{{ route('qr.poster') }}" target="_blank" class="btn-primary w-full mt-2">🖨 Cetak Poster</a>
    </div>

    {{-- Pengaturan + panduan --}}
    <div class="lg:col-span-3 space-y-5">
        <form action="{{ route('qr.simpan') }}" method="POST" class="card p-6">
            @csrf
            <h3 class="font-semibold text-slate-700 mb-1">Alamat Situs</h3>
            <p class="text-xs text-slate-400 mb-4">Alamat yang dibuka tamu setelah scan. Halaman tujuannya otomatis <code>/mulai</code>.</p>

            <label class="lbl">Alamat dasar</label>
            <input type="url" name="base" value="{{ old('base', $base) }}" required class="inp"
                   placeholder="http://192.168.1.103:8000 atau https://bukutamu.contoh.go.id">

            <button class="btn-primary mt-4">💾 Simpan & Perbarui QR</button>
        </form>

        <div class="card p-6 text-sm text-slate-600">
            <h3 class="font-semibold text-slate-700 mb-3">Panduan alamat</h3>
            <ul class="space-y-2 list-disc pl-5">
                <li><b>Uji coba di kantor:</b> HP tamu dan laptop harus satu WiFi. Pakai alamat IP laptop, misalnya <code>http://192.168.1.103:8000</code> (cek dengan <code>ipconfig</code>). Kalau WiFi berganti, IP berubah dan QR perlu dibuat ulang.</li>
                <li><b>Pemakaian sungguhan:</b> pasang di hosting atau server dengan domain tetap (sebaiknya HTTPS), lalu isi alamat domain di sini. QR yang dicetak tidak perlu diganti lagi.</li>
                <li>Setelah alamat berubah, cetak ulang poster agar QR di dinding sesuai.</li>
            </ul>
        </div>
    </div>
</div>

<script>
function salin() {
    const box = document.getElementById('linkBox');
    box.select();
    (navigator.clipboard ? navigator.clipboard.writeText(box.value) : Promise.reject())
        .catch(() => document.execCommand('copy'))
        .finally(() => alert('Link disalin'));
}
</script>
@endsection