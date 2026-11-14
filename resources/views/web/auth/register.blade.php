@extends('web.layouts.public')
@section('title', 'Daftar')

@section('content')
<div class="flex min-h-screen flex-col items-center justify-center gap-4 px-5 py-10 md:flex-row md:gap-16 lg:gap-28">

    @include('web.partials.guest-badge', [
        'formId'       => 'registerForm',
        'source'       => 'nama',
        'mode'         => 'name',
        'stamp'        => 'TERDAFTAR',
        'status'       => 'Menunggu pendaftaran',
        'hideOnMobile' => true,
    ])

    <main class="card w-full max-w-md rounded-3xl p-7 sm:p-9">
        <div class="mb-7 flex items-center gap-3">
            @if($set->logo_url)
                <img src="{{ $set->logo_url }}" alt="Logo" class="h-10 w-10 rounded-xl bg-white/10 object-contain p-1 ring-1 ring-white/15">
            @endif
            <span class="font-display text-base font-semibold text-white/80">{{ $set->nama_pendek }}</span>
        </div>

        <h1 class="font-display text-4xl font-extrabold tracking-tight">Buat akun</h1>
        <p class="mt-2 text-white/60">Daftar untuk mengajukan kunjungan terjadwal.</p>

        @if($errors->any())
            <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl border border-red-400/30 bg-red-500/10 p-3 text-sm text-red-200">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form id="registerForm" action="{{ route('register.post') }}" method="POST" class="mt-8 space-y-5">
            @csrf

            <div class="field">
                <label for="nama" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Nama lengkap</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <input id="nama" class="inp" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama sesuai identitas">
                </div>
            </div>

            <div class="field">
                <label for="phone" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">No. HP</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="3"/><path d="M11 18h2"/></svg>
                    <input id="phone" class="inp" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" inputmode="tel" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="field">
                <label for="email" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Email</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/></svg>
                    <input id="email" class="inp" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
                </div>
            </div>

            <div class="field">
                <label for="password" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Password</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="3"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
                    <input id="password" class="inp pr-12" type="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Minimal 6 karakter">
                    <button type="button" data-toggle-pw="password,password_confirmation" aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-white/45 transition hover:text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/30">
                        <svg class="eye-on h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 4.1-5M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                    </button>
                </div>
            </div>

            <div class="field">
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Ulangi password</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 4.5 3.2 8 8 9 4.8-1 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>
                    <input id="password_confirmation" class="inp" type="password" name="password_confirmation" required minlength="6" autocomplete="new-password" placeholder="Ketik ulang password">
                </div>
            </div>

            <button type="submit" data-loading="Mendaftarkan..."
                    class="btn flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3.5 font-semibold text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/40 disabled:cursor-not-allowed disabled:opacity-85">
                <span data-label>Daftar</span>
                <span data-spin class="spinner hidden"></span>
            </button>
        </form>

        <p class="mt-7 text-center text-sm text-white/55">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-amber underline-offset-4 hover:underline">Masuk</a>
        </p>
    </main>
</div>
@endsection