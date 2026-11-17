@extends('web.layouts.public')
@section('title', 'Isi Data Tamu')

@push('styles')
<style>
    select.inp option { background: #141d55; color: #fff; }
    .ico.top { top: 1.15rem; transform: none; }
    select.inp { appearance: none; -webkit-appearance: none; padding-right: 2.5rem; }
</style>
@endpush

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center gap-4 px-5 py-10 md:flex-row md:gap-16 lg:gap-28">

    @include('web.partials.guest-badge', [
        'formId'       => 'walkinForm',
        'source'       => 'name',
        'mode'         => 'name',
        'stamp'        => 'TERCATAT',
        'status'       => 'Menunggu resepsionis',
        'hideOnMobile' => true,
    ])

    <main class="card w-full max-w-md rounded-3xl p-7 sm:p-9">
        <a href="{{ route('mulai') }}" class="inline-flex items-center gap-1.5 text-sm text-white/55 transition hover:text-white">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
            Kembali
        </a>

        <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight">Data tamu</h1>
        <p class="mt-2 text-white/60">Isi data Anda, resepsionis {{ $set->nama_pendek }} akan memprosesnya.</p>

        @if($errors->any())
            <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl border border-red-400/30 bg-red-500/10 p-3 text-sm text-red-200">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form id="walkinForm" action="{{ route('walkin.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-5">
            @csrf

            {{-- jebakan bot, jangan dihapus --}}
            <div style="position:absolute;left:-9999px" aria-hidden="true">
                <label>Website</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="field">
                <label for="name" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Nama lengkap *</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <input id="name" class="inp" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Nama sesuai identitas">
                </div>
            </div>

            <div class="field">
                <label for="institution" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Instansi / asal</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16M16 9h2a2 2 0 0 1 2 2v10M2 21h20M8 7h4M8 11h4M8 15h4"/></svg>
                    <input id="institution" class="inp" type="text" name="institution" value="{{ old('institution') }}" autocomplete="organization" placeholder="Nama instansi atau perusahaan">
                </div>
            </div>

            <div class="field">
                <label for="phone" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">No. HP</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="3"/><path d="M11 18h2"/></svg>
                    <input id="phone" class="inp" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            @php
                $ikon = [
                    'berkas'     => 'M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5ZM14 3v5h5M9 13h6M9 17h6',
                    'temu'       => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75',
                    'konsultasi' => 'M21 12a8 8 0 0 1-11.5 7.2L3 21l1.8-6A8 8 0 1 1 21 12Z',
                    'lainnya'    => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3M12 17h.01',
                ];
            @endphp

            <div class="field">
                <span class="mb-2 block text-sm font-medium text-white/70">Jenis keperluan *</span>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(\App\Support\JenisKeperluan::DAFTAR as $kode => $j)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="purpose_type" value="{{ $kode }}" data-info="{{ $j['info'] }}" required
                                   class="peer sr-only" @checked(old('purpose_type') === $kode)>
                            <span class="flex h-full flex-col gap-2 rounded-2xl border border-white/15 bg-white/5 p-3 transition hover:bg-white/10 peer-checked:border-amber peer-checked:bg-amber/10 peer-checked:[&_svg]:text-amber peer-focus-visible:ring-4 peer-focus-visible:ring-brand/40">
                                <svg class="h-6 w-6 text-white/60 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $ikon[$kode] ?? $ikon['lainnya'] }}"/></svg>
                                <span class="text-sm font-semibold leading-tight">{{ $j['label'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <p id="jenisInfo" class="mt-2 min-h-[1.25rem] text-xs text-amber/90"></p>
            </div>

            <div class="field">
                <label for="purpose" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Keterangan *</label>
                <div class="relative">
                    <svg class="ico top h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.5 7.2L3 21l1.8-6A8 8 0 1 1 21 12Z"/></svg>
                    <textarea id="purpose" class="inp resize-none" name="purpose" rows="2" required placeholder="Jelaskan singkat keperluan Anda">{{ old('purpose') }}</textarea>
                </div>
            </div>

            <div class="field">
                <label for="employee_id" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Yang dituju <span id="empStar" class="hidden text-amber">*</span></label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="11" r="2.5"/><path d="M5.5 17a4 4 0 0 1 7 0M15 9h3M15 13h3"/></svg>
                    <select id="employee_id" class="inp" name="employee_id">
                        <option value="">Belum tahu / tanyakan resepsionis</option>
                        @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" @selected(old('employee_id') == $p->id)>
                                {{ $p->name }}{{ $p->position ? ' — '.$p->position : '' }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>

            <div class="field">
                <label for="photo" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Foto <span class="font-normal text-white/40">(opsional)</span></label>
                <div class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-3">
                    <input id="photo" type="file" name="photo" accept="image/*" capture="user"
                           class="w-full text-sm text-white/55 file:mr-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-white/10 file:px-4 file:py-2.5 file:font-medium file:text-white hover:file:bg-white/15">
                </div>
            </div>

            <button type="submit" data-loading="Mengirim..."
                    class="btn flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3.5 font-semibold text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/40 disabled:cursor-not-allowed disabled:opacity-85">
                <span data-label>Kirim data</span>
                <span data-spin class="spinner hidden"></span>
            </button>
        </form>
    </main>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var radios = document.querySelectorAll('input[name="purpose_type"]');
        var emp  = document.getElementById('employee_id');
        var star = document.getElementById('empStar');
        var info = document.getElementById('jenisInfo');
        var perluPegawai = ['temu', 'konsultasi'];

        function sync() {
            var r = document.querySelector('input[name="purpose_type"]:checked');
            var butuh = r && perluPegawai.indexOf(r.value) > -1;
            emp.required = !!butuh;
            star.classList.toggle('hidden', !butuh);
            info.textContent = r ? r.dataset.info : '';
        }
        radios.forEach(function (r) { r.addEventListener('change', sync); });
        sync();
    })();
</script>
@endpush