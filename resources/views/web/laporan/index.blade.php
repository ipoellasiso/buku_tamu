@extends('web.layouts.app')
@section('title', 'Laporan')
@section('header', 'Laporan Tamu')
@section('crumb', 'Laporan')
@section('subtitle', 'Rekap kunjungan terjadwal dan tamu langsung — '.\Carbon\Carbon::parse($dari)->format('d/m/Y').' s/d '.\Carbon\Carbon::parse($sampai)->format('d/m/Y'))

@section('content')
<form method="GET" action="{{ route('laporan.index') }}" class="card p-4 mb-5">
    <div class="flex flex-wrap items-end gap-3">
        <div>
            <label class="lbl">Tanggal dari</label>
            <input type="date" name="dari" value="{{ $dari }}" class="inp">
        </div>
        <div>
            <label class="lbl">Tanggal sampai</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="inp">
        </div>
        <div>
            <label class="lbl">Sumber</label>
            <select name="sumber" class="inp">
                <option value="">— Semua —</option>
                <option value="terjadwal" @selected($sumber === 'terjadwal')>Terjadwal</option>
                <option value="langsung"  @selected($sumber === 'langsung')>Tamu Langsung</option>
            </select>
        </div>
        <button class="btn-primary">🔍 Tampilkan</button>
        <a href="{{ route('laporan.index') }}" class="btn-outline">↺ Reset</a>

        <div class="flex flex-wrap gap-2 sm:ml-auto">
            <a href="{{ route('laporan.pdf', ['dari' => $dari, 'sampai' => $sampai, 'sumber' => $sumber]) }}"
               class="btn-outline !text-red-600 !border-red-300 hover:!bg-red-50">📄 Export PDF</a>
            <a href="{{ route('laporan.excel', ['dari' => $dari, 'sampai' => $sampai, 'sumber' => $sumber]) }}"
               class="btn-outline !text-emerald-700 !border-emerald-300 hover:!bg-emerald-50">📊 Export Excel</a>
        </div>
    </div>
</form>

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-5">
    @foreach([
        ['Total Kunjungan', $stats['total'],     'bg-blue-50 text-blue-600',     '👥'],
        ['Terjadwal',       $stats['terjadwal'], 'bg-sky-50 text-sky-600',       '🗓'],
        ['Tamu Langsung',   $stats['langsung'],  'bg-purple-50 text-purple-600', '🏢'],
        ['Selesai',         $stats['selesai'],   'bg-green-50 text-green-600',   '🏁'],
        ['Sedang Di Dalam', $stats['didalam'],   'bg-teal-50 text-teal-600',     '🚪'],
        ['Menunggu',        $stats['pending'],   'bg-orange-50 text-orange-600', '⏳'],
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

<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-600">☰ Daftar Kunjungan</h3>
        <span class="text-xs font-semibold bg-indigo-50 text-[#3f5bd9] rounded-full px-3 py-1">{{ $stats['total'] }}</span>
    </div>

    <div class="overflow-x-auto p-5">
        <table class="text-sm border border-slate-200 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Nama Tamu</th>
                    <th class="px-4 py-3 text-left">Keperluan</th>
                    <th class="px-4 py-3 text-left">Yang Dituju</th>
                    <th class="px-4 py-3 text-left">Waktu</th>
                    <th class="px-4 py-3 text-left">Sumber</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $v)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $v->dibuat?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $v->nama }}</p>
                        @if($v->instansi !== '-')<p class="text-xs text-slate-400">{{ $v->instansi }}</p>@endif
                    </td>
                    <td class="px-4 py-3">{{ $v->keperluan }}</td>
                    <td class="px-4 py-3">{{ $v->tujuan }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $v->waktu?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">@include('web.partials.sumber', ['sumber' => $v->sumber])</td>
                    <td class="px-4 py-3">@include('web.partials.status', ['status' => $v->status])</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">Tidak ada data pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection