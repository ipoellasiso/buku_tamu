@extends('web.layouts.app')
@section('title', 'Detail Tamu')
@section('header', 'Detail Tamu')
@section('crumb', 'Daftar Tamu')

@section('content')
@php $f = fn ($t) => $t ? $t->format('d/m/Y H:i') : '-'; @endphp

<div class="card p-6 max-w-2xl">
    <div class="flex items-center gap-4 mb-5">
        @if($tamu->foto)
            <img src="{{ asset('storage/'.$tamu->foto) }}" class="w-16 h-16 rounded-full object-cover" alt="">
        @else
            <span class="w-16 h-16 rounded-full bg-slate-100 grid place-items-center text-2xl text-slate-400">👤</span>
        @endif
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-lg truncate">{{ $tamu->nama }}</h3>
            <p class="text-sm text-slate-400">{{ $tamu->instansi }}</p>
        </div>
        <div class="flex flex-col items-end gap-1">
            @include('web.partials.status', ['status' => $tamu->status])
            @include('web.partials.sumber', ['sumber' => $tamu->sumber])
        </div>
    </div>

    <dl class="divide-y divide-slate-100 text-sm">
        @foreach([
            ['No. HP',       $tamu->hp],
            ['Keperluan',    $tamu->keperluan],
            ['Yang Dituju',  $tamu->tujuan],
            ['Catatan',      $tamu->catatan ?: '-'],
            [$tamu->sumber === 'langsung' ? 'Waktu Masuk' : 'Jadwal', $f($tamu->waktu)],
            ['Selesai',      $f($tamu->selesai)],
            ['Didaftarkan',  $f($tamu->dibuat)],
        ] as [$l, $v])
        <div class="flex justify-between gap-4 py-3">
            <dt class="text-slate-500 shrink-0">{{ $l }}</dt>
            <dd class="font-medium text-right">{{ $v }}</dd>
        </div>
        @endforeach
    </dl>

    @if($tamu->status === 'confirmed')
    <form method="POST" class="mt-5"
          action="{{ $tamu->sumber === 'langsung' ? route('walkin.selesai', $tamu->id) : route('permohonan.complete', $tamu->id) }}">
        @csrf
        <button class="btn-green w-full !py-2.5">Selesaikan Kunjungan</button>
    </form>
    @endif
</div>

<a href="{{ route('tamu.index') }}" class="inline-block mt-4 text-[#3f5bd9] text-sm">← Kembali</a>
@endsection