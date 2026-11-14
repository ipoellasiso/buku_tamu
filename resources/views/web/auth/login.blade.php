<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
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
                        ink: '#060B24',
                        brand: '#2F5BFF',
                        violet: '#7C5CFF',
                        amber: '#FFC247',
                    },
                },
            },
        }
    </script>
    <style>
        html, body { background: #060B24; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ===== Latar: aurora + grid ===== */
        .aurora { position: fixed; inset: 0; overflow: hidden; z-index: 0; }
        .blob { position: absolute; border-radius: 9999px; filter: blur(90px); opacity: .55; will-change: transform; }
        .blob.b1 { width: 46vmax; height: 46vmax; left: -12vmax; top: -14vmax; background: #2F5BFF; animation: drift1 18s ease-in-out infinite alternate; }
        .blob.b2 { width: 40vmax; height: 40vmax; right: -12vmax; bottom: -16vmax; background: #7C5CFF; animation: drift2 22s ease-in-out infinite alternate; }
        .blob.b3 { width: 22vmax; height: 22vmax; left: 38%; top: 55%; background: #FFC247; opacity: .16; animation: drift3 26s ease-in-out infinite alternate; }
        @keyframes drift1 { to { transform: translate(10vmax, 8vmax) scale(1.1); } }
        @keyframes drift2 { to { transform: translate(-9vmax, -7vmax) scale(1.15); } }
        @keyframes drift3 { to { transform: translate(-12vmax, -10vmax) scale(.8); } }
        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(75% 75% at 50% 50%, #000 30%, transparent);
            -webkit-mask-image: radial-gradient(75% 75% at 50% 50%, #000 30%, transparent);
        }

        /* ===== Lanyard + badge ===== */
        .hang {
            transform-origin: 50% 0;
            animation: drop .95s cubic-bezier(.2, .9, .3, 1.2) both, sway 6s ease-in-out .95s infinite;
        }
        @keyframes drop {
            from { transform: translateY(-120%) rotate(-6deg); opacity: 0; }
            to   { transform: translateY(0) rotate(0); opacity: 1; }
        }
        @keyframes sway {
            0%, 100% { transform: rotate(2.5deg); }
            50%      { transform: rotate(-2.5deg); }
        }
        .tilt-wrap { perspective: 900px; }
        .tilt { transition: transform .15s ease-out; transform-style: preserve-3d; }

        .badge {
            position: relative; overflow: hidden;
            background: linear-gradient(150deg, rgba(47,91,255,.95) 0%, rgba(124,92,255,.9) 100%);
            border: 1px solid rgba(255,255,255,.28);
            box-shadow: 0 30px 70px -15px rgba(47,91,255,.7), inset 0 1px 0 rgba(255,255,255,.35);
        }
        /* kilau holografik mengikuti kursor */
        .badge::before {
            content: ""; position: absolute; inset: 0;
            background: radial-gradient(260px circle at var(--mx, 30%) var(--my, 20%), rgba(255,255,255,.35), transparent 60%);
            pointer-events: none;
        }
        .badge::after {
            content: ""; position: absolute; top: -20%; bottom: -20%; left: -60%; width: 40%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.4), transparent);
            transform: skewX(-18deg);
            animation: shine 5.5s ease-in-out 1.4s infinite;
            pointer-events: none;
        }
        @keyframes shine { 0%, 60% { left: -60%; } 100% { left: 160%; } }

        .stamp { opacity: 0; transform: rotate(-14deg) scale(2.2); pointer-events: none; }
        .stamp.on { animation: stamp .45s cubic-bezier(.3, 1.6, .5, 1) forwards; }
        @keyframes stamp {
            from { opacity: 0; transform: rotate(-14deg) scale(2.2); }
            to   { opacity: 1; transform: rotate(-14deg) scale(1); }
        }
        .name-swap { transition: opacity .15s ease; }
        .name-swap.out { opacity: 0; }

        /* ===== Kartu form kaca ===== */
        .card {
            background: linear-gradient(160deg, rgba(255,255,255,.09), rgba(255,255,255,.03));
            border: 1px solid rgba(255,255,255,.12);
            -webkit-backdrop-filter: blur(24px);
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 80px -30px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.12);
            transition: box-shadow .3s ease, border-color .3s ease;
            animation: cardIn .8s ease .2s both;
        }
        .card:focus-within {
            border-color: rgba(111,140,255,.55);
            box-shadow: 0 30px 90px -25px rgba(47,91,255,.65), inset 0 1px 0 rgba(255,255,255,.15);
        }
        @keyframes cardIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

        .inp {
            width: 100%;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.14);
            color: #fff;
            border-radius: 14px;
            padding: .85rem 1rem .85rem 2.9rem;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .inp::placeholder { color: rgba(255,255,255,.35); }
        .inp:focus {
            outline: none;
            background: rgba(255,255,255,.09);
            border-color: #6F8CFF;
            box-shadow: 0 0 0 4px rgba(47,91,255,.25);
        }
        /* matikan warna kuning autofill browser */
        .inp:-webkit-autofill,
        .inp:-webkit-autofill:hover,
        .inp:-webkit-autofill:focus {
            -webkit-text-fill-color: #fff;
            caret-color: #fff;
            -webkit-box-shadow: 0 0 0 1000px #141d55 inset;
            box-shadow: 0 0 0 1000px #141d55 inset;
            border-color: rgba(255,255,255,.2);
            transition: background-color 9999s ease-in-out 0s;
        }
        .ico { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,.4); transition: color .2s; pointer-events: none; }
        .field:focus-within .ico { color: #FFC247; }
        .field:focus-within label { color: #fff; }

        /* ===== Tombol ===== */
        .btn {
            position: relative; overflow: hidden;
            background: linear-gradient(100deg, #2F5BFF, #7C5CFF);
            box-shadow: 0 14px 34px -10px rgba(79,91,255,.8);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn:hover { filter: brightness(1.12); box-shadow: 0 18px 40px -10px rgba(111,100,255,.9); }
        .btn:active { transform: scale(.985); }
        .btn::after {
            content: ""; position: absolute; top: 0; bottom: 0; left: -50%; width: 35%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg);
            transition: left .6s ease;
        }
        .btn:hover::after { left: 130%; }

        .spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 9999px; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .badge.pop { animation: pop 1.1s cubic-bezier(.3, 1.4, .5, 1); }
        @keyframes pop {
            0%   { transform: scale(1); box-shadow: 0 30px 70px -15px rgba(47,91,255,.7); }
            30%  { transform: scale(1.06) rotate(-1.5deg); box-shadow: 0 0 0 6px rgba(255,194,71,.55), 0 30px 80px -10px rgba(255,194,71,.55); }
            100% { transform: scale(1); box-shadow: 0 30px 70px -15px rgba(47,91,255,.7); }
        }
        @media (prefers-reduced-motion: reduce) { .badge.pop { animation: none; } }

        @media (prefers-reduced-motion: reduce) {
            .blob, .hang, .badge::after, .card, .spinner { animation: none !important; }
            .stamp.on { animation-duration: .01s; }
            .tilt { transition: none; }
        }
    </style>
</head>
<body class="min-h-screen text-white antialiased">

<div class="aurora" aria-hidden="true">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
    <div class="grid-bg"></div>
</div>

<div class="relative z-10 flex min-h-screen flex-col items-center justify-center gap-4 px-5 py-10 md:flex-row md:gap-16 lg:gap-28">

    {{-- ============ Badge tamu ============ --}}
    <section class="flex flex-col items-center">
        <div class="hang flex origin-top scale-[.82] flex-col items-center md:scale-100">
            <div class="h-10 w-3 rounded-b bg-gradient-to-b from-amber to-amber/60 shadow md:h-24"></div>
            <div class="-mt-1 h-3 w-8 rounded-sm bg-white/40"></div>

            <div class="tilt-wrap">
                <div id="tilt" class="tilt">
                    <div id="badge" class="badge w-72 rounded-3xl p-6 text-white">
                        <div class="mx-auto mb-4 h-2 w-16 rounded-full bg-white/30"></div>

                        <div id="feedPill" class="mb-3 hidden items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-amber"></span>
                            <span id="feedPillText">Tamu terbaruasas</span>
                        </div>

                        <div class="flex items-center gap-4">
                            <div id="avatar" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-amber font-display text-3xl font-extrabold text-ink">T</div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-white/70">Tamu</p>
                                <p id="guestName" class="name-swap break-words font-display text-xl font-extrabold leading-tight">Tamu Baru</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white/12 px-3 py-2 ring-1 ring-white/15">
                                <p class="text-[11px] text-white/70">Tanggal</p>
                                <p id="guestDate" class="font-semibold">—</p>
                            </div>
                            <div class="rounded-xl bg-white/12 px-3 py-2 ring-1 ring-white/15">
                                <p class="text-[11px] text-white/70">Jam datang</p>
                                <p id="guestTime" class="font-semibold tabular-nums">—</p>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between border-t border-dashed border-white/30 pt-4">
                            <p id="statusText" class="text-xs text-white/70">Menunggu masuk</p>
                            <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-amber"></span>
                        </div>

                        <div id="stamp" class="stamp absolute bottom-14 right-4 rounded-xl border-4 border-amber bg-ink/70 px-4 py-1 font-display text-3xl font-extrabold text-amber">
                            HADIR
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-6 hidden max-w-xs text-center text-sm text-white/55 md:block">
            Setiap tamu tercatat. Masuk untuk melihat siapa saja yang sudah datang hari ini.
        </p>
    </section>

    {{-- ============ Form ============ --}}
    <main class="card w-full max-w-md rounded-3xl p-7 sm:p-9">
        <div class="mb-7 flex items-center gap-3">
            @if($set->logo_url)
                <img src="{{ $set->logo_url }}" alt="Logo" class="h-10 w-10 rounded-xl bg-white/10 object-contain p-1 ring-1 ring-white/15">
            @endif
            <span class="font-display text-base font-semibold text-white/80">{{ $set->nama_pendek }}</span>
        </div>

        <h1 class="font-display text-4xl font-extrabold tracking-tight">Selamat datang</h1>
        <p class="mt-2 text-white/60">Masuk ke {{ $set->nama_aplikasi }} dengan akun Anda.</p>

        @if($errors->any())
            <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl border border-red-400/30 bg-red-500/10 p-3 text-sm text-red-200">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V7a1 1 0 0 0-1-1Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login.post') }}" method="POST" class="mt-8 space-y-5">
            @csrf

            <div class="field">
                <label for="email" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Email</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 8 9 6 9-6"/></svg>
                    <input id="email" class="inp" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
                </div>
            </div>

            <div class="field">
                <label for="password" class="mb-1.5 block text-sm font-medium text-white/70 transition-colors">Password</label>
                <div class="relative">
                    <svg class="ico h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="3"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
                    <input id="password" class="inp pr-12" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
                    <button type="button" id="togglePw" aria-label="Tampilkan password"
                            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-white/45 transition hover:text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/30">
                        <svg id="eyeOn" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg id="eyeOff" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.9 10.9 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 4.1-5M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                    </button>
                </div>
            </div>

            <button id="submitBtn" type="submit"
                    class="btn flex w-full items-center justify-center gap-2 rounded-xl px-4 py-3.5 font-semibold text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-brand/40 disabled:cursor-not-allowed disabled:opacity-85">
                <span id="btnLabel">Masuk</span>
                <span id="btnSpin" class="spinner hidden"></span>
            </button>
        </form>

        <p class="mt-7 text-center text-sm text-white/55">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-amber underline-offset-4 hover:underline">Daftar</a>
        </p>
    </main>
</div>

<script>
    (function () {
        var FEED = @json(route('notif.publik'));

        var email = document.getElementById('email');
        var pw = document.getElementById('password');
        var nameEl = document.getElementById('guestName');
        var avatar = document.getElementById('avatar');
        var stamp = document.getElementById('stamp');
        var form = document.getElementById('loginForm');
        var btn = document.getElementById('submitBtn');
        var label = document.getElementById('btnLabel');
        var spin = document.getElementById('btnSpin');
        var tilt = document.getElementById('tilt');
        var badge = document.getElementById('badge');
        var dateEl = document.getElementById('guestDate');
        var timeEl = document.getElementById('guestTime');
        var pill = document.getElementById('feedPill');
        var pillText = document.getElementById('feedPillText');
        var statusEl = document.getElementById('statusText');

        function fmtDate(d) { return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }); }
        function fmtTime(d) { return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':'); }

        // ===== Badge: dua mode =====
        // feed   = menampilkan tamu terbaru dari server (tamu langsung / permohonan)
        // typing = nama mengikuti email yang sedang diketik
        var feed = { items: [], idx: 0, hari: 0, now: 0, fetched: 0 };
        var lastNewest = null;
        var shownName = null;

        function typing() { return email.value.trim() !== ''; }

        function nameFromEmail(v) {
            var local = (v || '').split('@')[0].replace(/[._\-+0-9]+/g, ' ').trim();
            if (!local) return 'Tamu Baru';
            return local.split(/\s+/).slice(0, 3).map(function (w) {
                return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
            }).join(' ');
        }

        function rel(sec) {
            if (sec < 60) return 'baru saja';
            if (sec < 3600) return Math.floor(sec / 60) + ' menit lalu';
            return Math.floor(sec / 3600) + ' jam lalu';
        }

        function paint(name, when, pillTxt, statusTxt) {
            if (name !== shownName) {
                shownName = name;
                nameEl.classList.add('out');
                setTimeout(function () {
                    nameEl.textContent = name;
                    avatar.textContent = name.charAt(0).toUpperCase();
                    nameEl.classList.remove('out');
                }, 150);
            }
            dateEl.textContent = fmtDate(when);
            timeEl.textContent = fmtTime(when);
            statusEl.textContent = statusTxt;
            if (pillTxt) {
                pill.classList.remove('hidden'); pill.classList.add('inline-flex');
                pillText.textContent = pillTxt;
            } else {
                pill.classList.add('hidden'); pill.classList.remove('inline-flex');
            }
        }

        function render() {
            if (typing()) {
                paint(nameFromEmail(email.value), new Date(), '', 'Menunggu masuk');
                return;
            }
            var it = feed.items[feed.idx];
            if (!it) {
                paint('Tamu Baru', new Date(), '', 'Menunggu masuk');
                return;
            }
            var nowEst = feed.now + (Date.now() - feed.fetched) / 1000;
            var jenis = it.tipe === 'langsung' ? 'Tamu langsung' : 'Permohonan';
            paint(it.nama, new Date(it.at * 1000), jenis + ' · ' + rel(Math.max(0, nowEst - it.at)),
                  feed.hari > 0 ? feed.hari + ' tamu hari ini' : 'Belum ada tamu hari ini');
        }

        function pop() {
            badge.classList.remove('pop');
            void badge.offsetWidth;
            badge.classList.add('pop');
            setTimeout(function () { badge.classList.remove('pop'); }, 1200);
        }

        function poll() {
            fetch(FEED, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (d) {
                    if (!d || !d.items) return;
                    var newest = d.items.length ? d.items[0].key : '';
                    var fresh = lastNewest !== null && newest !== '' && newest !== lastNewest;
                    lastNewest = newest;
                    feed.items = d.items; feed.hari = d.hari; feed.now = d.now; feed.fetched = Date.now();
                    if (fresh) { feed.idx = 0; if (!typing()) pop(); }
                    else if (feed.idx >= feed.items.length) { feed.idx = 0; }
                    render();
                })
                .catch(function () {});
        }

        email.addEventListener('input', render);
        render();
        poll();
        setInterval(function () { if (!document.hidden) poll(); }, 10000);
        setInterval(render, 15000);
        // ganti-ganti tamu terbaru (maks. 3) kalau lebih dari satu
        setInterval(function () {
            if (typing() || feed.items.length < 2) return;
            feed.idx = (feed.idx + 1) % Math.min(feed.items.length, 3);
            render();
        }, 7000);

        // ===== Badge miring 3D mengikuti kursor + kilau =====
        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reduce) {
            window.addEventListener('pointermove', function (e) {
                var r = badge.getBoundingClientRect();
                if (!r.width) return;
                var dx = Math.max(-1, Math.min(1, (e.clientX - (r.left + r.width / 2)) / (window.innerWidth / 2)));
                var dy = Math.max(-1, Math.min(1, (e.clientY - (r.top + r.height / 2)) / (window.innerHeight / 2)));
                tilt.style.transform = 'rotateY(' + (dx * 14) + 'deg) rotateX(' + (-dy * 10) + 'deg)';
                badge.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
                badge.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
            });
        }

        // ===== Tampilkan / sembunyikan password =====
        var toggle = document.getElementById('togglePw');
        toggle.addEventListener('click', function () {
            var show = pw.type === 'password';
            pw.type = show ? 'text' : 'password';
            document.getElementById('eyeOn').classList.toggle('hidden', show);
            document.getElementById('eyeOff').classList.toggle('hidden', !show);
            toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });

        // ===== Submit: stempel HADIR dulu, lalu kirim form =====
        var sent = false;
        form.addEventListener('submit', function (e) {
            if (sent || !form.checkValidity()) return;
            e.preventDefault();
            sent = true;
            btn.disabled = true;
            label.textContent = 'Mencatat kehadiran...';
            spin.classList.remove('hidden');
            stamp.classList.add('on');
            setTimeout(function () { form.submit(); }, 700);
        });
    })();
</script>
</body>
</html>