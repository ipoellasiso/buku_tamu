<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#060B24">
    <title>@yield('title') — {{ $set->nama_aplikasi }}</title>
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
                    colors: { ink: '#060B24', brand: '#2F5BFF', violet: '#7C5CFF', amber: '#FFC247' },
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
        .hang { transform-origin: 50% 0; animation: drop .95s cubic-bezier(.2, .9, .3, 1.2) both, sway 6s ease-in-out .95s infinite; }
        @keyframes drop { from { transform: translateY(-120%) rotate(-6deg); opacity: 0; } to { transform: translateY(0) rotate(0); opacity: 1; } }
        @keyframes sway { 0%, 100% { transform: rotate(2.5deg); } 50% { transform: rotate(-2.5deg); } }
        .tilt-wrap { perspective: 900px; }
        .tilt { transition: transform .15s ease-out; transform-style: preserve-3d; }
        .badge {
            position: relative; overflow: hidden;
            background: linear-gradient(150deg, rgba(47,91,255,.95) 0%, rgba(124,92,255,.9) 100%);
            border: 1px solid rgba(255,255,255,.28);
            box-shadow: 0 30px 70px -15px rgba(47,91,255,.7), inset 0 1px 0 rgba(255,255,255,.35);
        }
        .badge::before {
            content: ""; position: absolute; inset: 0;
            background: radial-gradient(260px circle at var(--mx, 30%) var(--my, 20%), rgba(255,255,255,.35), transparent 60%);
            pointer-events: none;
        }
        .badge::after {
            content: ""; position: absolute; top: -20%; bottom: -20%; left: -60%; width: 40%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.4), transparent);
            transform: skewX(-18deg); animation: shine 5.5s ease-in-out 1.4s infinite; pointer-events: none;
        }
        @keyframes shine { 0%, 60% { left: -60%; } 100% { left: 160%; } }
        .stamp { opacity: 0; transform: rotate(-14deg) scale(2.2); pointer-events: none; }
        .stamp.on { animation: stamp .45s cubic-bezier(.3, 1.6, .5, 1) forwards; }
        @keyframes stamp { from { opacity: 0; transform: rotate(-14deg) scale(2.2); } to { opacity: 1; transform: rotate(-14deg) scale(1); } }
        .name-swap { transition: opacity .15s ease; }
        .name-swap.out { opacity: 0; }

        /* ===== Kartu kaca ===== */
        .card {
            background: linear-gradient(160deg, rgba(255,255,255,.09), rgba(255,255,255,.03));
            border: 1px solid rgba(255,255,255,.12);
            -webkit-backdrop-filter: blur(24px);
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 80px -30px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.12);
            transition: box-shadow .3s ease, border-color .3s ease;
            animation: cardIn .8s ease .2s both;
        }
        .card:focus-within { border-color: rgba(111,140,255,.55); box-shadow: 0 30px 90px -25px rgba(47,91,255,.65), inset 0 1px 0 rgba(255,255,255,.15); }
        @keyframes cardIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

        /* ===== Input ===== */
        .inp {
            width: 100%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14);
            color: #fff; border-radius: 14px; padding: .85rem 1rem .85rem 2.9rem;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .inp::placeholder { color: rgba(255,255,255,.35); }
        .inp:focus { outline: none; background: rgba(255,255,255,.09); border-color: #6F8CFF; box-shadow: 0 0 0 4px rgba(47,91,255,.25); }
        .inp:-webkit-autofill, .inp:-webkit-autofill:hover, .inp:-webkit-autofill:focus {
            -webkit-text-fill-color: #fff; caret-color: #fff;
            -webkit-box-shadow: 0 0 0 1000px #141d55 inset; box-shadow: 0 0 0 1000px #141d55 inset;
            border-color: rgba(255,255,255,.2); transition: background-color 9999s ease-in-out 0s;
        }
        .ico { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,.4); transition: color .2s; pointer-events: none; }
        .field:focus-within .ico { color: #FFC247; }
        .field:focus-within label { color: #fff; }

        /* ===== Tombol ===== */
        .btn {
            position: relative; overflow: hidden; background: linear-gradient(100deg, #2F5BFF, #7C5CFF);
            box-shadow: 0 14px 34px -10px rgba(79,91,255,.8);
            transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        }
        .btn:hover { filter: brightness(1.12); box-shadow: 0 18px 40px -10px rgba(111,100,255,.9); }
        .btn:active { transform: scale(.985); }
        .btn::after {
            content: ""; position: absolute; top: 0; bottom: 0; left: -50%; width: 35%;
            background: linear-gradient(100deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg); transition: left .6s ease;
        }
        .btn:hover::after { left: 130%; }
        .spinner { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 9999px; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (prefers-reduced-motion: reduce) {
            .blob, .hang, .badge::after, .card, .spinner { animation: none !important; }
            .stamp.on { animation-duration: .01s; }
            .tilt { transition: none; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-white antialiased">

<div class="aurora" aria-hidden="true">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
    <div class="grid-bg"></div>
</div>

<div class="relative z-10 min-h-screen">
    @yield('content')
</div>

<script>
    // Tombol lihat/sembunyikan password (data-toggle-pw="id1,id2")
    document.querySelectorAll('[data-toggle-pw]').forEach(function (b) {
        b.addEventListener('click', function () {
            var ids = b.dataset.togglePw.split(',');
            var show = document.getElementById(ids[0]).type === 'password';
            ids.forEach(function (id) { document.getElementById(id).type = show ? 'text' : 'password'; });
            b.querySelector('.eye-on').classList.toggle('hidden', show);
            b.querySelector('.eye-off').classList.toggle('hidden', !show);
            b.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });
</script>
@stack('scripts')
</body>
</html>