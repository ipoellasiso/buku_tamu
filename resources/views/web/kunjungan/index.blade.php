@extends('web.layouts.portal')
@section('title', 'Beranda')

@section('content')
@php
    $user    = auth()->user();
    $isGuest = $user->role === 'guest';
    $unread  = $notifikasi->where('is_read', false)->count();

    // rapikan tanggal ISO di teks notifikasi lama
    $rapi = fn ($t) => preg_replace_callback(
        '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?/',
        fn ($m) => \Carbon\Carbon::parse($m[0])->format('d/m/Y H:i'),
        $t
    );

    $border = [
        'pending'   => 'border-orange-400',
        'approved'  => 'border-blue-500',
        'confirmed' => 'border-teal-500',
        'completed' => 'border-green-500',
        'rejected'  => 'border-red-400',
    ];
    $icon = ['pending' => '⏳', 'approved' => '✅', 'confirmed' => '🏢', 'completed' => '🏁', 'rejected' => '✖️'];
@endphp

{{-- Sambutan --}}
<div class="rounded-2xl bg-gradient-to-br from-blue-800 to-blue-600 text-white p-5 shadow-lg mb-4">
    <p class="text-blue-100 text-sm">Selamat datang,</p>
    <h1 class="text-xl font-bold leading-tight">{{ $user->name }}</h1>
    <p class="text-xs text-blue-200 mt-1">
        {{ $isGuest ? 'Ajukan kunjungan ke BPKAD Kota Palu dengan mudah.' : 'Daftar tamu yang ingin bertemu Anda.' }}
    </p>
    @if($isGuest)
    <a href="{{ route('kunjungan.create') }}"
       class="mt-4 inline-flex items-center gap-2 bg-white text-blue-800 font-semibold text-sm px-4 py-2.5 rounded-xl shadow active:scale-95 transition">
        ➕ Ajukan Kunjungan
    </a>
    @endif
</div>

{{-- Ringkasan --}}
<div class="grid grid-cols-3 gap-3 mb-4">
    @foreach([
        ['Menunggu', $ringkasan['menunggu'], 'text-orange-600', 'bg-orange-50'],
        ['Aktif',    $ringkasan['aktif'],    'text-blue-600',   'bg-blue-50'],
        ['Selesai',  $ringkasan['selesai'],  'text-green-600',  'bg-green-50'],
    ] as [$label, $val, $txt, $bg])
    <div class="rounded-2xl {{ $bg }} p-3 text-center">
        <p class="text-2xl font-bold {{ $txt }}">{{ $val }}</p>
        <p class="text-[11px] text-slate-500">{{ $label }}</p>
    </div>
    @endforeach
</div>

{{-- Notifikasi (bisa dilipat) --}}
@if($notifikasi->isNotEmpty())
<details class="bg-white rounded-2xl shadow-sm mb-4 overflow-hidden" {{ $unread > 0 ? 'open' : '' }}>
    <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none list-none">
        <span class="font-semibold text-sm">🔔 Notifikasi</span>
        @if($unread > 0)
            <span class="text-[11px] bg-red-500 text-white rounded-full px-2 py-0.5">{{ $unread }} baru</span>
        @endif
    </summary>
    <div class="divide-y border-t">
        @foreach($notifikasi->take(3) as $n)
        <div class="px-4 py-3 text-sm {{ $n->is_read ? '' : 'bg-blue-50/60' }}">
            <p class="font-medium">{{ $n->title }}</p>
            <p class="text-slate-600 text-[13px] leading-snug">{{ $rapi($n->body) }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $n->created_at->format('d/m/Y H:i') }}</p>
        </div>
        @endforeach
    </div>
</details>
@endif

{{-- Daftar kunjungan --}}
<h2 class="text-sm font-semibold text-slate-500 mb-2 px-1">{{ $isGuest ? 'Riwayat Kunjungan' : 'Tamu Saya' }}</h2>

<div class="space-y-3">
    @forelse($kunjungan as $k)
    <a href="{{ route('kunjungan.show', $k->id) }}"
       class="flex items-center gap-3 bg-white rounded-2xl shadow-sm border-l-4 {{ $border[$k->status] ?? 'border-slate-300' }} p-4 active:bg-slate-50 transition">
        <div class="w-10 h-10 shrink-0 rounded-full bg-slate-100 grid place-items-center text-lg">
            {{ $icon[$k->status] ?? '•' }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-semibold truncate">{{ $k->purpose }}</p>
            <p class="text-xs text-slate-500 truncate">
                {{ $isGuest ? 'Bertemu '.($k->employee?->name ?? '-') : 'Tamu: '.($k->guestUser?->name ?? '-') }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">
                🗓 {{ $k->scheduled_at ? \Carbon\Carbon::parse($k->scheduled_at)->format('d/m/Y H:i') : 'Menunggu jadwal' }}
            </p>
        </div>
        <div class="shrink-0">@include('web.partials.status', ['status' => $k->status])</div>
    </a>
    @empty
    <div class="bg-white rounded-2xl p-8 text-center">
        <p class="text-4xl mb-2">📭</p>
        <p class="text-slate-500 text-sm">Belum ada kunjungan.</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $kunjungan->links() }}</div>
@endsection