@extends('web.layouts.app')
@section('title', 'Pengaturan')
@section('header', 'Pengaturan Instansi')
@section('crumb', 'Pengaturan')
@section('subtitle', 'Nama, logo, dan identitas yang tampil di seluruh aplikasi, laporan, dan poster')

@section('content')
<div class="grid lg:grid-cols-5 gap-5">

    <form action="{{ route('pengaturan.simpan') }}" method="POST" enctype="multipart/form-data"
          class="card p-6 lg:col-span-3 space-y-4">
        @csrf

        <div>
            <label class="lbl">Nama aplikasi *</label>
            <input id="f_app" name="nama_aplikasi" required class="inp" placeholder="Buku Tamu Digital"
                   value="{{ old('nama_aplikasi', $set->nama_aplikasi) }}">
            <p class="text-xs text-slate-400 mt-1">Judul utama di header, halaman login, dan poster.</p>
        </div>

        <div>
            <label class="lbl">Nama instansi (lengkap) *</label>
            <input id="f_inst" name="nama_instansi" required class="inp"
                   placeholder="Badan Pengelola Keuangan dan Aset Daerah"
                   value="{{ old('nama_instansi', $set->nama_instansi) }}">
            <p class="text-xs text-slate-400 mt-1">Dipakai di kop laporan PDF.</p>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="lbl">Singkatan</label>
                <input id="f_sing" name="singkatan" class="inp" placeholder="BPKAD"
                       value="{{ old('singkatan', $set->singkatan) }}">
            </div>
            <div>
                <label class="lbl">Lokasi</label>
                <input id="f_lok" name="lokasi" class="inp" placeholder="Kota Palu"
                       value="{{ old('lokasi', $set->lokasi) }}">
            </div>
        </div>

        <div>
            <label class="lbl">Alamat <span class="normal-case font-normal">(opsional, tampil di kop PDF)</span></label>
            <textarea name="alamat" rows="2" class="inp">{{ old('alamat', $set->alamat) }}</textarea>
        </div>

        <div>
            <label class="lbl">Logo <span class="normal-case font-normal">(opsional)</span></label>
            <input type="file" id="f_logo" name="logo" accept="image/png,image/jpeg,image/webp"
                   class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-blue-700 file:font-medium">
            <p class="text-xs text-slate-400 mt-1">PNG, JPG, atau WEBP, maks 2 MB. Latar transparan paling bagus.</p>
            @if($set->logo)
            <label class="flex items-center gap-2 text-sm mt-2">
                <input type="checkbox" name="hapus_logo" value="1"> Hapus logo saat ini
            </label>
            @endif
        </div>

        <button class="btn-primary">💾 Simpan Pengaturan</button>
    </form>

    <div class="lg:col-span-2 space-y-5">
        <div class="card p-6">
            <p class="lbl">Pratinjau</p>
            <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50">
                <img id="pvLogo" src="{{ $set->logo_url }}" alt=""
                     class="h-12 w-12 object-contain {{ $set->logo_url ? '' : 'hidden' }}">
                <span id="pvEmoji"
                      class="w-11 h-11 rounded-xl bg-[#1e2a78] text-white grid place-items-center text-xl {{ $set->logo_url ? 'hidden' : '' }}">📖</span>
                <div class="leading-tight min-w-0">
                    <p id="pvApp" class="font-extrabold text-[#1e2a78] uppercase truncate"></p>
                    <p id="pvInst" class="text-xs italic text-slate-400 truncate"></p>
                </div>
            </div>
        </div>

        <div class="card p-6 text-sm text-slate-600">
            <p class="font-semibold text-slate-700 mb-2">Tampil di</p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Sidebar dan header panel admin</li>
                <li>Halaman login, daftar, dan halaman sambutan QR</li>
                <li>Poster QR dan kop laporan PDF</li>
                <li>Notifikasi "terima kasih" saat kunjungan selesai</li>
            </ul>
        </div>
    </div>
</div>

<script>
    const q = id => document.getElementById(id);

    function pendek() {
        const sing = q('f_sing').value.trim(), inst = q('f_inst').value.trim(), lok = q('f_lok').value.trim();
        return ((sing || inst) + ' ' + lok).trim();
    }
    function paint() {
        q('pvApp').textContent  = q('f_app').value.trim() || 'Nama Aplikasi';
        q('pvInst').textContent = pendek() || 'Nama Instansi';
    }

    ['f_app', 'f_inst', 'f_sing', 'f_lok'].forEach(id => q(id).addEventListener('input', paint));

    q('f_logo').addEventListener('change', e => {
        const f = e.target.files[0];
        if (!f) return;
        q('pvLogo').src = URL.createObjectURL(f);
        q('pvLogo').classList.remove('hidden');
        q('pvEmoji').classList.add('hidden');
    });

    paint();
</script>
@endsection