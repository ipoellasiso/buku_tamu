@extends('web.layouts.app')
@section('title', isset($pegawai) ? 'Edit Pegawai' : 'Tambah Pegawai')
@section('header', isset($pegawai) ? 'Edit Pegawai' : 'Tambah Pegawai')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-xl">
    <form action="{{ isset($pegawai) ? route('pegawai.update', $pegawai->id) : route('pegawai.store') }}"
          method="POST" class="space-y-4">
        @csrf
        @if(isset($pegawai)) @method('PUT') @endif

        @foreach([
            ['name',       'Nama *',   'text'],
            ['email',      'Email *',  'email'],
            ['position',   'Jabatan',  'text'],
            ['department', 'Bidang',   'text'],
            ['phone',      'No. HP',   'text'],
        ] as [$field, $label, $type])
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $field }}"
                   value="{{ old($field, $pegawai->$field ?? '') }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        @endforeach

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
            <select name="role" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="employee" @selected(old('role', $pegawai->role ?? 'employee') === 'employee')>Pegawai</option>
                <option value="admin"    @selected(old('role', $pegawai->role ?? '') === 'admin')>Admin</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Password {{ isset($pegawai) ? '(kosongkan jika tidak diubah)' : '*' }}
            </label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="flex gap-3">
            <button class="px-6 py-2 bg-blue-700 text-white rounded-lg text-sm font-semibold hover:bg-blue-800">Simpan</button>
            <a href="{{ route('pegawai.index') }}" class="px-6 py-2 bg-gray-200 rounded-lg text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection