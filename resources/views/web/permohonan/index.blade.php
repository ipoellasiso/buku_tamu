@extends('web.layouts.app')
@section('title', 'Permohonan')
@section('header', 'Permohonan Kunjungan')
@section('crumb', 'Kunjungan')
@section('subtitle', 'Daftar permohonan kunjungan tamu yang perlu diproses')

@section('content')
<form method="GET" class="card p-4 mb-5">
    <div class="flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <label class="lbl">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="inp" placeholder="Nama tamu, pegawai, keperluan...">
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
            <label class="lbl">Status</label>
            <select name="status" class="inp">
                <option value="">— Semua —</option>
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'confirmed' => 'Di Dalam', 'completed' => 'Selesai', 'rejected' => 'Ditolak'] as $v => $l)
                    <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary">🔍 Tampilkan</button>
        <a href="{{ route('permohonan.index') }}" class="btn-outline">↺ Reset</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-600">☰ Daftar Permohonan</h3>
        <span class="text-xs font-semibold bg-indigo-50 text-[#3f5bd9] rounded-full px-3 py-1">{{ $permohonan->total() }}</span>
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
                    <th class="px-4 py-3 text-left">Tamu</th>
                    <th class="px-4 py-3 text-left">Yang Dituju</th>
                    <th class="px-4 py-3 text-left">Keperluan</th>
                    <th class="px-4 py-3 text-left">Jadwal</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permohonan as $p)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500">{{ $permohonan->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $p->guestUser?->name ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $p->guestUser?->phone }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $p->employee?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($p->purpose, 32) }}</td>
                    <td class="px-4 py-3 text-slate-500">
                        {{ $p->scheduled_at ? \Carbon\Carbon::parse($p->scheduled_at)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3">@include('web.partials.status', ['status' => $p->status])</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('permohonan.show', $p->id) }}" class="ic bg-cyan-500 hover:bg-cyan-600" title="Detail">👁</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 pb-5">{{ $permohonan->links() }}</div>
</div>
@endsection