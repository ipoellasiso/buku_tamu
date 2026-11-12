@extends('web.layouts.app')
@section('title', 'Pegawai')
@section('header', 'Manajemen Pegawai')

@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('pegawai.create') }}" class="px-4 py-2 bg-blue-700 text-white rounded-lg text-sm hover:bg-blue-800">+ Tambah Pegawai</a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-blue-800 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Jabatan</th>
                <th class="px-4 py-3 text-left">Bidang</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($pegawai as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                <td class="px-4 py-3">{{ $p->email }}</td>
                <td class="px-4 py-3">{{ $p->position ?? '-' }}</td>
                <td class="px-4 py-3">{{ $p->department ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs {{ $p->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $p->role === 'admin' ? 'Admin' : 'Pegawai' }}
                    </span>
                </td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="{{ route('pegawai.edit', $p->id) }}" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600">Edit</a>
                    @if($p->id !== auth()->id())
                    <form action="{{ route('pegawai.destroy', $p->id) }}" method="POST"
                          onsubmit="return confirm('Hapus pegawai ini?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada pegawai</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $pegawai->links() }}</div>
</div>
@endsection