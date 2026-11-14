@extends('web.layouts.public')
@section('title', $set->nama_pendek)

@section('content')
<main class="flex min-h-screen flex-col items-center justify-center px-5 py-10">

    <div class="mb-7 text-center">
        @if($set->logo_url)
            <img src="{{ $set->logo_url }}" alt="Logo" class="mx-auto mb-4 h-16 w-16 rounded-2xl bg-white/10 object-contain p-2 ring-1 ring-white/15">
        @else
            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-white/10 text-3xl ring-1 ring-white/15">📖</div>
        @endif
        <h1 class="font-display text-3xl font-extrabold tracking-tight">{{ $set->nama_aplikasi }}</h1>
        <p class="mt-1 text-sm text-white/60">{{ $set->nama_pendek }}</p>
    </div>

    <div class="card w-full max-w-sm rounded-3xl p-6">
        <h2 class="font-display text-xl font-extrabold">Selamat datang</h2>
        <p class="mb-5 mt-1 text-sm text-white/60">Pilih sesuai keperluan Anda.</p>

        <a href="{{ route('walkin.create') }}"
           class="group flex items-center gap-4 rounded-2xl border border-amber/50 bg-amber/10 p-4 transition hover:bg-amber/15 focus:outline-none focus-visible:ring-4 focus-visible:ring-amber/30 active:scale-[.98]">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-amber text-ink">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/></svg>
            </span>
            <span class="min-w-0">
                <span class="block font-display font-extrabold">Saya sudah di kantor</span>
                <span class="mt-0.5 block text-xs text-white/60">Isi data tamu langsung tanpa akun. Resepsionis akan memprosesnya.</span>
            </span>
            <svg class="ml-auto h-5 w-5 shrink-0 text-amber transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
        </a>

        <a href="{{ route('register') }}"
           class="group mt-3 flex items-center gap-4 rounded-2xl border border-white/15 bg-white/5 p-4 transition hover:bg-white/10 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/30 active:scale-[.98]">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/10 text-white ring-1 ring-white/15">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></svg>
            </span>
            <span class="min-w-0">
                <span class="block font-display font-extrabold">Ajukan kunjungan terjadwal</span>
                <span class="mt-0.5 block text-xs text-white/60">Buat akun, pilih pegawai dan waktu. Setelah disetujui Anda mendapat QR.</span>
            </span>
            <svg class="ml-auto h-5 w-5 shrink-0 text-white/50 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
        </a>

        <p class="mt-6 text-center text-sm text-white/55">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-amber underline-offset-4 hover:underline">Masuk</a>
        </p>
    </div>

    <p class="mt-8 text-xs text-white/40">© {{ now()->year }} {{ $set->nama_pendek }}</p>
</main>
@endsection