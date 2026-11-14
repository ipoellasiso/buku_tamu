<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — {{ $set->nama_aplikasi }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Bricolage Grotesque"', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        navy: '#0A1F5C',
                        brand: '#2F5BFF',
                        amber: '#FFC247',
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Latar kiri: grid halus + cahaya */
        .stage {
            background:
                radial-gradient(60% 50% at 20% 15%, rgba(47, 91, 255, .55), transparent 70%),
                radial-gradient(50% 45% at 85% 90%, rgba(255, 194, 71, .22), transparent 70%),
                #0A1F5C;
        }
        .stage::before {
            content: "";
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(70% 70% at 50% 50%, #000, transparent);
            -webkit-mask-image: radial-gradient(70% 70% at 50% 50%, #000, transparent);
        }

        /* Lanyard + badge: jatuh dari atas lalu berayun pelan */
        .hang {
            transform-origin: 50% 0;
            animation: drop .9s cubic-bezier(.2, .9, .3, 1.2) both, sway 6s ease-in-out .9s infinite;
        }
        @keyframes drop {
            from { transform: translateY(-120%) rotate(-6deg); opacity: 0; }
            to   { transform: translateY(0) rotate(0); opacity: 1; }
        }
        @keyframes sway {
            0%, 100% { transform: rotate(2.5deg); }
            50%      { transform: rotate(-2.5deg); }
        }

        /* Kilau tipis yang lewat di badge */
        .badge { position: relative; overflow: hidden; }
        .badge::after {
            content: "";
            position: absolute; top: -20%; bottom: -20%; left: -60%; width: 40%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.55), transparent);
            transform: skewX(-18deg);
            animation: shine 5s ease-in-out 1.4s infinite;
        }
        @keyframes shine {
            0%, 60% { left: -60%; }
            100%    { left: 160%; }
        }

        /* Stempel HADIR saat submit */
        .stamp {
            opacity: 0;
            transform: rotate(-14deg) scale(2.2);
            pointer-events: none;
        }
        .stamp.on {
            animation: stamp .45s cubic-bezier(.3, 1.6, .5, 1) forwards;
        }
        @keyframes stamp {
            from { opacity: 0; transform: rotate(-14deg) scale(2.2); }
            to   { opacity: 1; transform: rotate(-14deg) scale(1); }
        }

        /* Nama berganti halus */
        .name-swap { transition: opacity .15s ease; }
        .name-swap.out { opacity: 0; }

        /* Form masuk */
        .form-in { animation: formIn .7s ease .15s both; }
        @keyframes formIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: none; }
        }

        .field:focus-within label { color: #2F5BFF; }

        .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,.4);
            border-top-color: #fff;
            border-radius: 9999px;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (prefers-reduced-motion: reduce) {
            .hang, .badge::after, .form-in, .spinner { animation: none !important; }
            .stamp.on { animation-duration: .01s; }
        }
    </style>
</head>
<body class="min-h-screen bg-white text-slate-800 antialiased">

<div class="min-h-screen grid lg:grid-cols-[1.05fr_1fr]">

    {{-- ============ KIRI: panggung badge tamu ============ --}}
    <aside class="stage relative hidden lg:flex flex-col items-center justify-between overflow-hidden px-12 py-12 text-white">
        <div class="relative z-10 flex items-center gap-3 self-start">
            @if($set->logo_url)
                <img src="{{ $set->logo_url }}" alt="Logo" class="h-10 w-10 rounded-lg bg-white/90 p-1 object-contain">
            @endif
            <span class="font-display text-lg font-semibold tracking-tight">{{ $set->nama_pendek }}</span>
        </div>

        <div class="relative z-10 flex flex-col items-center">
            {{-- tali lanyard --}}
            <div class="hang flex flex-col items-center">
                <div class="h-24 w-3 rounded-b bg-gradient-to-b from-amber to-amber/70 shadow"></div>
                <div class="-mt-1 h-3 w-8 rounded-sm bg-slate-300 shadow-inner"></div>

                {{-- badge --}}
                <div class="badge relative w-72 rounded-3xl bg-white p-6 text-slate-900 shadow-2xl shadow-black/40">
                    <div class="mx-auto mb-5 h-2 w-16 rounded-full bg-slate-200"></div>

                    <div class="flex items-center gap-4">
                        <div id="avatar" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-brand font-display text-3xl font-extrabold text-white">T</div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500">Tamu</p>
                            <p id="guestName" class="name-swap font-display text-xl font-extrabold leading-tight break-words">Tamu Baru</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-slate-100 px-3 py-2">
                            <p class="text-[11px] text-slate-500">Tanggal</p>
                            <p id="guestDate" class="font-semibold">—</p>
                        </div>
                        <div class="rounded-xl bg-slate-100 px-3 py-2">
                            <p class="text-[11px] text-slate-500">Jam datang</p>
                            <p id="guestTime" class="font-semibold tabular-nums">—</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-dashed border-slate-300 pt-4">
                        <p class="text-xs text-slate-500">Menunggu masuk</p>
                        <span class="h-2.5 w-2.5 rounded-full bg-amber"></span>
                    </div>

                    {{-- stempel --}}
                    <div id="stamp" class="stamp absolute right-4 bottom-14 rounded-xl border-4 border-emerald-600 px-4 py-1 font-display text-3xl font-extrabold text-emerald-600 bg-white/70">
                        HADIR
                    </div>
                </div>
            </div>
        </div>

        <p class="relative z-10 max-w-sm text-center text-sm text-blue-100/80">
            Setiap tamu tercatat. Masuk untuk melihat siapa saja yang sudah datang hari ini.
        </p>
    </aside>

    {{-- ============ KANAN: form ============ --}}
    <main class="flex items-center justify-center px-6 py-12 sm:px-12">
        <div class="form-in w-full max-w-sm">

            {{-- header khusus mobile --}}
            <div class="mb-8 lg:hidden">
                @if($set->logo_url)
                    <img src="{{ $set->logo_url }}" alt="Logo" class="mb-4 h-12 object-contain">
                @endif
                <p class="text-sm font-semibold text-brand">{{ $set->nama_pendek }}</p>
            </div>

            <h1 class="font-display text-4xl font-extrabold tracking-tight text-navy">Selamat datang</h1>
            <p class="mt-2 text-slate-500">Masuk ke {{ $set->nama_aplikasi }} dengan akun Anda.</p>

            @if($errors->any())
                <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form id="loginForm" action="{{ route('login.post') }}" method="POST" class="mt-8 space-y-5">
                @csrf

                <div class="field">
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700 transition-colors">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="nama@email.com"
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 transition focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15">
                </div>

                <div class="field">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700 transition-colors">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="Masukkan password"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-12 text-slate-900 placeholder-slate-400 transition focus:border-brand focus:outline-none focus:ring-4 focus:ring-brand/15">
                        <button type="button" id="togglePw" aria-label="Tampilkan password"
                                class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-slate-400 transition hover:text-slate-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/15">
                            <svg id="eyeOn" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="eyeOff" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 4.1-5M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

                <button id="submitBtn" type="submit"
                        class="group flex w-full items-center justify-center gap-2 rounded-xl bg-navy px-4 py-3.5 font-semibold text-white shadow-lg shadow-navy/25 transition hover:bg-brand hover:shadow-brand/30 focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/30 active:scale-[.99] disabled:cursor-not-allowed disabled:opacity-80">
                    <span id="btnLabel">Masuk</span>
                    <span id="btnSpin" class="spinner hidden"></span>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-slate-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-brand underline-offset-4 hover:underline">Daftar</a>
            </p>
        </div>
    </main>
</div>

<script>
    (function () {
        const email = document.getElementById('email');
        const pw = document.getElementById('password');
        const nameEl = document.getElementById('guestName');
        const avatar = document.getElementById('avatar');
        const stamp = document.getElementById('stamp');
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('submitBtn');
        const label = document.getElementById('btnLabel');
        const spin = document.getElementById('btnSpin');

        // Tanggal & jam di badge
        const dateEl = document.getElementById('guestDate');
        const timeEl = document.getElementById('guestTime');
        function tick() {
            const now = new Date();
            dateEl.textContent = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
        }
        tick();
        setInterval(tick, 15000);

        // Nama di badge mengikuti isi email
        function nameFromEmail(v) {
            const local = (v || '').split('@')[0].replace(/[._\-+0-9]+/g, ' ').trim();
            if (!local) return 'Tamu Baru';
            return local.split(/\s+/).slice(0, 3)
                .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
        }
        let last = nameEl.textContent;
        function updateName() {
            const next = nameFromEmail(email.value);
            if (next === last) return;
            last = next;
            nameEl.classList.add('out');
            setTimeout(() => {
                nameEl.textContent = next;
                avatar.textContent = next.charAt(0);
                nameEl.classList.remove('out');
            }, 150);
        }
        email.addEventListener('input', updateName);
        updateName();

        // Tampilkan / sembunyikan password
        const toggle = document.getElementById('togglePw');
        toggle.addEventListener('click', () => {
            const show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            document.getElementById('eyeOn').classList.toggle('hidden', show);
            document.getElementById('eyeOff').classList.toggle('hidden', !show);
            toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });

        // Submit: stempel HADIR dulu, lalu kirim form
        let sent = false;
        form.addEventListener('submit', function (e) {
            if (sent) return;
            if (!form.checkValidity()) return;
            e.preventDefault();
            sent = true;
            btn.disabled = true;
            label.textContent = 'Mencatat kehadiran...';
            spin.classList.remove('hidden');
            stamp.classList.add('on');
            setTimeout(() => form.submit(), 700);
        });
    })();
</script>
</body>
</html>