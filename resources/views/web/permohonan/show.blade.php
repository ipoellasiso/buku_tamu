@extends('web.layouts.app')
@section('title', 'Detail Permohonan')
@section('header', 'Detail Permohonan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">Informasi Permohonan</h3>
            @include('web.partials.status', ['status' => $permohonan->status])
        </div>
        <dl class="space-y-3 text-sm">
            <div class="flex"><dt class="w-36 text-gray-500">Tamu</dt><dd class="font-medium">{{ $permohonan->guestUser?->name ?? '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">No. HP</dt><dd>{{ $permohonan->guestUser?->phone ?? '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Yang Dituju</dt><dd class="font-medium">{{ $permohonan->employee?->name ?? '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Jabatan</dt><dd>{{ $permohonan->employee?->position ?? '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Keperluan</dt><dd>{{ $permohonan->purpose }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Catatan</dt><dd>{{ $permohonan->notes ?? '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Waktu Diminta</dt>
                <dd>{{ $permohonan->requested_at ? \Carbon\Carbon::parse($permohonan->requested_at)->format('d/m/Y H:i') : '-' }}</dd></div>
            <div class="flex"><dt class="w-36 text-gray-500">Jadwal</dt>
                <dd>{{ $permohonan->scheduled_at ? \Carbon\Carbon::parse($permohonan->scheduled_at)->format('d/m/Y H:i') : '-' }}</dd></div>
            @if($permohonan->employee_notes)
            <div class="flex"><dt class="w-36 text-gray-500">Catatan Admin</dt><dd>{{ $permohonan->employee_notes }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="space-y-4">
        @if($permohonan->qr_code_path)
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h3 class="font-semibold mb-3">QR Code Tamu</h3>
            <img src="{{ asset('storage/' . $permohonan->qr_code_path) }}" alt="QR Code" class="w-48 h-48 mx-auto">
        </div>
        @endif

        @if($permohonan->status === 'pending')
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold mb-4">Proses Permohonan</h3>
            <form action="{{ route('permohonan.approve', $permohonan->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jadwal Kedatangan *</label>
                    <input type="datetime-local" name="scheduled_at" required
                           class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea name="employee_notes" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <button class="w-full bg-green-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-green-700">
                    Setujui & Buat QR
                </button>
            </form>

            <form action="{{ route('permohonan.reject', $permohonan->id) }}" method="POST" class="mt-4 pt-4 border-t">
                @csrf
                <textarea name="employee_notes" placeholder="Alasan penolakan..." rows="2"
                          class="w-full border rounded-lg px-3 py-2 text-sm mb-2"></textarea>
                <button class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-red-700">Tolak</button>
            </form>
        </div>
        @endif

        @if($permohonan->status === 'confirmed')
        <div class="bg-white rounded-xl shadow p-6">
            <form action="{{ route('permohonan.complete', $permohonan->id) }}" method="POST">
                @csrf
                <button class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                    Selesaikan Kunjungan
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('permohonan.index') }}" class="inline-block mt-4 text-blue-600 text-sm">← Kembali</a>
@endsection