<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e2a78">
    <title>@yield('title', 'Admin') — {{ $set->nama_aplikasi }}</title>
    <script>if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');</script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style type="text/tailwindcss">
        @layer components {
            .card   { @apply bg-white rounded-2xl shadow-sm border border-slate-100; }
            .lbl    { @apply block text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1; }
            .inp    { @apply w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500; }
            .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium bg-[#3f5bd9] text-white hover:bg-[#3349b8] transition active:scale-95; }
            .btn-outline { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition active:scale-95; }
            .btn-green   { @apply inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition active:scale-95; }
            .ic     { @apply w-8 h-8 inline-grid place-items-center rounded-md text-white text-sm; }
        }
    </style>

    <style>
        input, select, textarea { font-size: 16px; }
        @media (min-width: 768px) { input, select, textarea { font-size: 14px; } }

        /* gaya tabel global (berlaku untuk semua halaman admin) */
        main table { width: 100%; }
        main thead { background: #f8fafc !important; color: #475569 !important; }
        main thead th { font-size: 11px; letter-spacing: .04em; text-transform: uppercase; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        main tbody td { border-bottom: 1px solid #f1f5f9; }
        main .overflow-hidden { overflow-x: auto; }
        main table { min-width: 680px; }

        /* mode gelap */
        html.dark body { background: #0b1220; color: #cbd5e1; }
        html.dark #sidebar { background: #111827; border-color: #1f2937; }
        html.dark #sidebar p { color: #e5e7eb; }
        html.dark #sidebar a:not(.active) { color: #cbd5e1; }
        html.dark #sidebar a:not(.active):hover { background: #1f2937; }
        html.dark .card, html.dark main .bg-white { background: #1e293b !important; border-color: #334155; }
        html.dark main h1 { color: #f1f5f9; }
        html.dark main thead { background: #0f172a !important; color: #94a3b8 !important; }
        html.dark main thead th, html.dark main tbody td { border-color: #334155 !important; }
        html.dark main tr:hover td { background: #243247; }
        html.dark main .divide-y > * { border-color: #334155; }
        html.dark main input, html.dark main select, html.dark main textarea { background: #0f172a !important; color: #e2e8f0; border-color: #334155; }
        html.dark main .text-slate-700, html.dark main .text-gray-800, html.dark main .text-gray-700 { color: #e2e8f0 !important; }
    </style>
</head>

@php
    $ico = [
        'home'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'list'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'qr'    => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
        'staff' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        'chart' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'door' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
        'gear' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        'speaker' => 'M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z',
    ];
    $sections = [
        'Home'       => [['dashboard',        'dashboard',    'Dashboard',        'home']],
        'Kunjungan'  => [
            ['permohonan.index', 'permohonan.*', 'Permohonan',       'list'],
            ['tamu.index',       'tamu.*',       'Daftar Tamu',      'users'],
            ['scan.index',       'scan.*',       'Scan QR',          'qr'],
            ['walkin.index', 'walkin.*', 'Tamu Langsung', 'door'],
            ['pengumuman.index', 'pengumuman.*', 'Pengumuman Suara', 'speaker'],
        ],
        'Pengaturan' => [
            ['pegawai.index', 'pegawai.*', 'Manajemen Pegawai', 'staff'],
            ['qr.index',      'qr.*',      'QR Situs',          'qr'],
            ['pengaturan.index', 'pengaturan.*', 'Pengaturan Instansi', 'gear'],
        ],
        'Laporan'    => [['laporan.index',    'laporan.*',    'Laporan',          'chart']],
    ];
    $user = auth()->user();
@endphp

<body class="bg-[#f3f5f9] text-slate-700 antialiased">
<div class="flex h-screen overflow-hidden">

    <div id="overlay" onclick="toggleMenu()" class="hidden fixed inset-0 bg-black/40 z-30 md:hidden"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-200 flex flex-col
                  -translate-x-full md:translate-x-0 md:static transition-transform duration-200">

        <div class="px-6 pt-6 pb-1">
            <div class="flex items-center justify-center gap-3">
                @if($set->logo_url)
                    <img src="{{ $set->logo_url }}" alt="Logo" class="h-12 w-auto max-w-[56px] object-contain">
                @else
                    <span class="w-11 h-11 rounded-xl bg-[#1e2a78] text-white grid place-items-center text-xl">📖</span>
                @endif
                <div class="leading-tight min-w-0">
                    <p class="text-lg font-extrabold text-[#1e2a78] tracking-wide uppercase truncate">{{ $set->nama_aplikasi }}</p>
                    <p class="text-xs italic text-slate-400 truncate">{{ $set->nama_pendek }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-3 py-4 text-slate-400 text-sm">
            <span>☀️</span>
            <button id="themeBtn" onclick="toggleTheme()" class="relative w-11 h-6 rounded-full bg-slate-300 transition" aria-label="Ganti tema">
                <span id="themeKnob" class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform"></span>
            </button>
            <span>🌙</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-5 pb-4">
            @foreach($sections as $title => $items)
                <p class="px-1 pt-5 pb-2 text-[15px] font-bold text-slate-900">{{ $title }}</p>
                <div class="space-y-1">
                    @foreach($items as [$route, $pattern, $label, $icon])
                        @php $active = request()->routeIs($pattern); @endphp
                        <a href="{{ route($route) }}"
                           class="{{ $active ? 'active bg-[#3f5bd9] text-white shadow' : 'text-slate-700 hover:bg-slate-100' }} flex items-center gap-3 px-4 py-2.5 rounded-xl text-[15px] transition">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $ico[$icon] }}"/>
                            </svg>
                            {{ $label }}
                            @if($route === 'walkin.index' && ($pend = \App\Models\Guest::where('status', 'pending')->count()) > 0)
                                <span class="ml-auto text-[11px] bg-red-500 text-white rounded-full px-2 py-0.5">{{ $pend }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="px-5 py-4 border-t border-slate-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- KOLOM KANAN --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

        <header class="sticky top-0 z-20 bg-[#1e2a78] text-white">
            <div class="px-4 md:px-8 h-[76px] flex items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <button onclick="toggleMenu()" class="text-2xl leading-none text-white/90 hover:text-white" aria-label="Menu">☰</button>
                    <div class="min-w-0">
                        <p class="font-semibold text-base md:text-lg truncate">Selamat Datang, {{ $user->name }} 👋</p>
                        <p class="text-[11px] md:text-xs text-blue-200 truncate">Ringkasan kunjungan tamu {{ $set->nama_pendek }} — Tahun {{ now()->year }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right hidden sm:block leading-tight">
                        <p class="font-semibold text-sm">{{ $user->name }}</p>
                        <p class="text-xs text-blue-200">{{ $user->role === 'admin' ? 'Admin' : 'Pegawai' }}</p>
                    </div>
                    <span class="w-10 h-10 rounded-full bg-white/20 grid place-items-center font-semibold">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>
                </div>
            </div>
        </header>

        <div class="px-4 md:px-8 pt-6 pb-10">
            {{-- breadcrumb --}}
            <nav class="flex justify-end items-center gap-2 text-sm text-[#3f5bd9] mb-1">
                <a href="{{ route('dashboard') }}" aria-label="Dashboard">🏠</a>
                @hasSection('crumb')
                    <span class="text-slate-400">/</span><span>@yield('crumb')</span>
                @endif
                <span class="text-slate-400">/</span><span>@yield('header')</span>
            </nav>

            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">@yield('header')</h1>
            @hasSection('subtitle')
                <p class="text-sm text-slate-500 mt-1">@yield('subtitle')</p>
            @endif

            <div class="mt-5">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl text-sm">{{ $errors->first() }}</div>
                @endif

                <main>
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</div>

@if($user->role === 'admin')
    @include('web.partials.notif-tamu')
@endif

<script>
    function toggleMenu() {
        const s = document.getElementById('sidebar'), o = document.getElementById('overlay');
        if (window.innerWidth >= 768) { s.classList.toggle('md:hidden'); return; }
        s.classList.toggle('-translate-x-full');
        o.classList.toggle('hidden');
    }

    function paintTheme() {
        const dark = document.documentElement.classList.contains('dark');
        document.getElementById('themeBtn').classList.toggle('bg-blue-600', dark);
        document.getElementById('themeBtn').classList.toggle('bg-slate-300', !dark);
        document.getElementById('themeKnob').style.transform = dark ? 'translateX(20px)' : '';
    }
    function toggleTheme() {
        const dark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', dark ? 'dark' : 'light');
        paintTheme();
    }
    paintTheme();
</script>
</body>
</html>