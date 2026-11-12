@extends('web.layouts.portal')
@section('title', 'Detail Kunjungan')

@section('content')
@php
    $steps = ['pending' => 'Diajukan', 'approved' => 'Disetujui', 'confirmed' => 'Di Dalam', 'completed' => 'Selesai'];
    $keys  = array_keys($steps);
    $idx   = array_search($kunjungan->status, $keys);
    $isOwner = auth()->id() === $kunjungan->guest_user_id;
    $tgl = fn ($t, $alt = '-') => $t ? \Carbon\Carbon::parse($t)->format('d/m/Y H:i') : $alt;
@endphp

<a href="{{ route('kunjungan.index') }}" class="inline-block text-blue-700 text-sm mb-3">← Kembali</a>

{{-- Tahapan --}}
@if($kunjungan->status === 'rejected')
<div class="rounded-2xl bg-red-50 border border-red-200 p-4 mb-4 text-red-700 text-sm">
    <p class="font-semibold">Permohonan ditolak</p>
    @if($kunjungan->employee_notes)<p class="mt-1">{{ $kunjungan->employee_notes }}</p>@endif
</div>
@else
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <div class="flex items-start">
        @foreach($steps as $key => $label)
            @php $done = $idx !== false && $loop->index <= $idx; @endphp
            <div class="flex flex-col items-center w-14 shrink-0">
                <div class="w-7 h-7 rounded-full grid place-items-center text-xs font-bold
                    {{ $done ? 'bg-blue-700 text-white' : 'bg-slate-200 text-slate-500' }}">
                    {{ $done ? '✓' : $loop->iteration }}
                </div>
                <span class="text-[10px] mt-1 text-center {{ $done ? 'text-blue-700 font-semibold' : 'text-slate-400' }}">{{ $label }}</span>
            </div>
            @unless($loop->last)
                <div class="flex-1 h-0.5 mt-3.5 {{ $idx !== false && $idx >= $loop->iteration ? 'bg-blue-700' : 'bg-slate-200' }}"></div>
            @endunless
        @endforeach
    </div>
</div>
@endif

{{-- QR --}}
@if($isOwner && $kunjungan->qr_code_path && in_array($kunjungan->status, ['approved', 'confirmed']))
<div class="bg-white rounded-2xl shadow-sm p-5 mb-4 text-center">
    <h2 class="font-semibold">QR Code Kunjungan</h2>
    <p class="text-xs text-slate-500 mb-3">Tunjukkan ke resepsionis saat tiba sesuai jadwal.</p>
    <img src="{{ asset('storage/'.$kunjungan->qr_code_path) }}" alt="QR Code"
         class="w-56 h-56 mx-auto rounded-xl border p-2 bg-white">
    <a href="{{ asset('storage/'.$kunjungan->qr_code_path) }}" download="qr-kunjungan-{{ $kunjungan->id }}.svg"
       class="inline-block mt-3 px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-sm font-medium">⬇ Simpan QR</a>
    <details class="mt-3 text-left">
        <summary class="text-xs text-slate-400 cursor-pointer text-center">Kode manual</summary>
        <p class="text-[11px] text-slate-500 break-all mt-2 text-center">{{ $kunjungan->qr_token }}</p>
    </details>
</div>
@endif

{{-- Detail --}}
<div class="bg-white rounded-2xl shadow-sm px-4 mb-4">
    @foreach([
        ['Tamu',          $kunjungan->guestUser?->name ?? '-'],
        ['Yang Dituju',   $kunjungan->employee?->name ?? '-'],
        ['Keperluan',     $kunjungan->purpose],
        ['Catatan',       $kunjungan->notes ?? '-'],
        ['Waktu Diminta', $tgl($kunjungan->requested_at)],
        ['Jadwal',        $tgl($kunjungan->scheduled_at, 'Belum ditentukan')],
    ] as [$label, $value])
    <div class="flex justify-between gap-4 py-3 border-b last:border-0 text-sm">
        <span class="text-slate-500 shrink-0">{{ $label }}</span>
        <span class="font-medium text-right">{{ $value }}</span>
    </div>
    @endforeach
    @if($kunjungan->employee_notes && $kunjungan->status !== 'rejected')
    <div class="flex justify-between gap-4 py-3 text-sm">
        <span class="text-slate-500 shrink-0">Catatan Admin</span>
        <span class="font-medium text-right">{{ $kunjungan->employee_notes }}</span>
    </div>
    @endif
</div>
@endsection