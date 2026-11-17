@php
    $kode = 'TL-' . str_pad($guest->id, 4, '0', STR_PAD_LEFT);
    $map = [
        'pending'   => ['pending',   'Menunggu resepsionis', 'Data Anda sudah terkirim. Silakan menuju resepsionis dan tunjukkan layar ini.', 'bg-amber/15 text-amber ring-amber/40', 'bg-amber'],
        'confirmed' => ['confirmed', 'Silakan masuk',        'Anda sudah tercatat masuk. Selamat berkunjung!',                                   'bg-cyan-400/15 text-cyan-300 ring-cyan-300/40', 'bg-cyan-300'],
        'completed' => ['completed', 'Kunjungan selesai',    'Terima kasih telah berkunjung ke ' . $set->nama_pendek . '.',                      'bg-emerald-400/15 text-emerald-300 ring-emerald-300/40', 'bg-emerald-300'],
    ];
    [$tipe, $judul, $ket, $cls, $dot] = $map[$guest->status] ?? $map['pending'];
@endphp

@extends('web.layouts.public')
@section('title', 'Status Tamu')

@push('styles')
    @if($guest->status !== 'completed')
        <meta http-equiv="refresh" content="10">
    @endif
@endpush

@section('content')
<main class="flex min-h-screen items-center justify-center px-5 py-10">
    <div class="card w-full max-w-sm rounded-3xl p-6 text-center">

        <div class="relative mx-auto h-20 w-20">
            @if($tipe === 'pending')
                <span class="absolute inset-0 animate-ping rounded-full {{ $dot }} opacity-20"></span>
            @endif
            <div class="relative grid h-20 w-20 place-items-center rounded-full ring-2 {{ $cls }}">
                @if($tipe === 'pending')
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                @elseif($tipe === 'confirmed')
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M2 21h20M8 8h4M8 12h4"/><path d="m16 15 2 2 4-4"/></svg>
                @else
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="m8 12.5 3 3 5-6"/></svg>
                @endif
            </div>
        </div>

        <h1 class="mt-5 font-display text-2xl font-extrabold">{{ $judul }}</h1>
        <p class="mt-1 text-sm text-white/60">{{ $ket }}</p>

        <div class="mt-6 rounded-2xl border border-dashed border-white/20 bg-white/5 py-4">
            <p class="text-xs text-white/55">Kode tamu</p>
            <p class="mt-1 font-display text-4xl font-extrabold tracking-widest text-amber">{{ $kode }}</p>
        </div>

        <dl class="mt-5 divide-y divide-white/10 text-left text-sm">
            @foreach([
                ['Nama',        $guest->name],
                ['Instansi',    $guest->institution ?? '-'],
                ['Keperluan',   $guest->purpose],
                ['Jenis',       \App\Support\JenisKeperluan::label($guest->purpose_type)],
                ['Yang dituju', $guest->host ?? 'Ditentukan resepsionis'],
                ['Dikirim',     $guest->created_at->format('d/m/Y H:i')],
            ] as [$l, $v])
            <div class="flex justify-between gap-4 py-2.5">
                <dt class="shrink-0 text-white/50">{{ $l }}</dt>
                <dd class="text-right font-medium text-white">{{ $v }}</dd>
            </div>
            @endforeach
        </dl>

        @if($guest->status !== 'completed')
            <p class="mt-4 text-xs text-white/40">Halaman ini diperbarui otomatis.</p>
        @endif

        <a href="{{ route('walkin.create') }}" class="mt-4 block text-sm font-semibold text-amber underline-offset-4 hover:underline">
            Isi data tamu lain
        </a>
    </div>
</main>
@endsection