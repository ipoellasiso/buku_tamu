@extends('web.layouts.portal')
@section('title', 'Ajukan Kunjungan')

@section('content')
<a href="{{ route('kunjungan.index') }}" class="inline-block text-blue-700 text-sm mb-3">← Kembali</a>

<div class="bg-white rounded-2xl shadow-sm p-5">
    <h1 class="text-lg font-bold">Ajukan Kunjungan</h1>
    <p class="text-xs text-slate-500 mb-5">Isi data berikut, admin akan memproses permohonan Anda.</p>

    <form action="{{ route('kunjungan.store') }}" method="POST" class="space-y-4">
        @csrf
        @php $inp = 'w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent'; @endphp

        <div>
            <label class="block text-sm font-medium mb-1.5">Yang Dituju *</label>
            <select name="employee_id" required class="{{ $inp }} bg-white">
                <option value="">— pilih pegawai —</option>
                @foreach($pegawai as $p)
                    <option value="{{ $p->id }}" @selected(old('employee_id') == $p->id)>
                        {{ $p->name }}{{ $p->position ? ' — '.$p->position : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Keperluan *</label>
            <input type="text" name="purpose" value="{{ old('purpose') }}" required class="{{ $inp }}">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Catatan <span class="text-slate-400 font-normal">(opsional)</span></label>
            <textarea name="notes" rows="3" class="{{ $inp }}">{{ old('notes') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Waktu yang Diinginkan *</label>
            <input type="datetime-local" name="requested_at" value="{{ old('requested_at') }}" required class="{{ $inp }}">
        </div>

        <button class="w-full bg-blue-700 text-white py-3.5 rounded-xl font-semibold shadow active:scale-[.98] transition">
            Kirim Permohonan
        </button>
    </form>
</div>
@endsection