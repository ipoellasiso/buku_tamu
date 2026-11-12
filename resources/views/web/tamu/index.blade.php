@extends('web.layouts.app')
@section('title', 'Daftar Tamu')
@section('header', 'Daftar Tamu')
@section('crumb', 'Kunjungan')
@section('subtitle', 'Tamu yang sudah masuk, dari kunjungan terjadwal maupun tamu langsung')

@section('content')
<form method="GET" class="card p-4 mb-5">
    <div class="flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-60">
            <label class="lbl">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="inp" placeholder="Nama, instansi, keperluan...">
        </div>
        <div>
            <label class="lbl">Tanggal dari</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="inp">
        </div>
        <div>
            <label class="lbl">Tanggal sampai</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="inp">
        </div>
        <div>
            <label class="lbl">Sumber</label>
            <select name="sumber" class="inp">
                <option value="">— Semua —</option>
                <option value="terjadwal" @selected(request('sumber') === 'terjadwal')>Terjadwal</option>
                <option value="langsung"  @selected(request('sumber') === 'langsung')>Langsung</option>
            </select>
        </div>
        <div>
            <label class="lbl">Status</label>
            <select name="status" class="inp">
                <option value="">— Semua —</option>
                <option value="confirmed" @selected(request('status') === 'confirmed')>Di Dalam</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
            </select>
        </div>
        <button class="btn-primary">🔍 Tampilkan</button>
        <a href="{{ route('tamu.index') }}" class="btn-outline">↺ Reset</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-600">☰ Daftar Tamu</h3>
        <span class="text-xs font-semibold bg-indigo-50 text-[#3f5bd9] rounded-full px-3 py-1">{{ $tamu->total() }}</span>
    </div>

    <form method="GET" class="px-5 pt-4 flex items-center gap-2 text-sm text-slate-500">
        @foreach(request()->except('per_page', 'page') as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        Tampilkan
        <select name="per_page" onchange="this.form.submit()" class="inp !w-auto !py-1">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
            @endforeach
        </select>
        data
    </form>

    <div class="overflow-x-auto p-5">
        <table class="text-sm border border-slate-200 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Nama Tamu</th>
                    <th class="px-4 py-3 text-left">Keperluan</th>
                    <th class="px-4 py-3 text-left">Yang Dituju</th>
                    <th class="px-4 py-3 text-left">Waktu</th>
                    <th class="px-4 py-3 text-left">Sumber</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tamu as $t)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500">{{ $tamu->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $t->nama }}</p>
                        @if($t->instansi !== '-')<p class="text-xs text-slate-400">{{ $t->instansi }}</p>@endif
                    </td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($t->keperluan, 32) }}</td>
                    <td class="px-4 py-3">{{ $t->tujuan }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $t->waktu?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="px-4 py-3">@include('web.partials.sumber', ['sumber' => $t->sumber])</td>
                    <td class="px-4 py-3">@include('web.partials.status', ['status' => $t->status])</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('tamu.show', [$t->sumber, $t->id]) }}" class="ic bg-cyan-500 hover:bg-cyan-600" title="Detail">👁</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">Belum ada tamu yang masuk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 pb-5">{{ $tamu->links() }}</div>
</div>
@endsection