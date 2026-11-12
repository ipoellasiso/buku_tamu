@extends('web.layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subtitle', 'Ringkasan kunjungan terjadwal dan tamu langsung')

@section('content')
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Tamu Hari Ini',       $stats['total_hari_ini'],     'bg-blue-50 text-blue-600',     '👥'],
        ['Sedang Di Dalam',     $stats['didalam'],            'bg-teal-50 text-teal-600',     '🏢'],
        ['Selesai Hari Ini',    $stats['selesai_hari_ini'],   'bg-green-50 text-green-600',   '🏁'],
        ['Kunjungan Bulan Ini', $stats['bulan_ini'],          'bg-indigo-50 text-indigo-600', '📅'],
        ['Permohonan Menunggu', $stats['permohonan_pending'], 'bg-orange-50 text-orange-600', '📋'],
        ['Menunggu di Lobi',    $stats['lobi_menunggu'],      'bg-rose-50 text-rose-600',     '⏳'],
        ['Total Pegawai',       $stats['total_pegawai'],      'bg-purple-50 text-purple-600', '👔'],
        ['Akun Tamu',           $stats['total_tamu'],         'bg-slate-100 text-slate-600',  '🙋'],
    ] as [$label, $val, $cls, $emoji])
    <div class="card p-4 flex items-center gap-3">
        <span class="w-11 h-11 shrink-0 rounded-xl grid place-items-center text-xl {{ $cls }}">{{ $emoji }}</span>
        <div>
            <p class="text-2xl font-bold text-slate-800 leading-none">{{ $val }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    {{-- Kunjungan terbaru (gabungan) --}}
    <div class="card">
        <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-slate-600">Kunjungan Terbaru</h3>
            <a href="{{ route('laporan.index') }}" class="text-[#3f5bd9] text-xs">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($tamu_terbaru as $t)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-sm truncate">{{ $t->nama }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $t->keperluan }}</p>
                    <div class="mt-1">@include('web.partials.sumber', ['sumber' => $t->sumber])</div>
                </div>
                @include('web.partials.status', ['status' => $t->status])
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-slate-400">Belum ada kunjungan</p>
            @endforelse
        </div>
    </div>

    {{-- Permohonan menunggu --}}
    <div class="card">
        <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-slate-600">Permohonan Menunggu</h3>
            <a href="{{ route('permohonan.index') }}" class="text-[#3f5bd9] text-xs">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($permohonan_pending as $p)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-sm truncate">{{ $p->guestUser?->name ?? '-' }}</p>
                    <p class="text-xs text-slate-400 truncate">→ {{ $p->employee?->name ?? '-' }} · {{ $p->purpose }}</p>
                </div>
                <a href="{{ route('permohonan.show', $p->id) }}" class="btn-primary !px-3 !py-1.5 !text-xs">Proses</a>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-slate-400">Tidak ada permohonan menunggu</p>
            @endforelse
        </div>
    </div>

    {{-- Menunggu di lobi --}}
    <div class="card">
        <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-slate-600">Menunggu di Lobi</h3>
            <a href="{{ route('walkin.index') }}" class="text-[#3f5bd9] text-xs">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($lobi as $g)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-sm truncate">{{ $g->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $g->purpose }} · {{ $g->created_at->format('H:i') }}</p>
                </div>
                <a href="{{ route('walkin.index') }}" class="btn-green !px-3 !py-1.5 !text-xs">Terima</a>
            </div>
            @empty
            <p class="px-5 py-6 text-sm text-slate-400">Tidak ada tamu menunggu</p>
            @endforelse
        </div>
    </div>
</div>
@endsection