<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e3a8a">
    <title>@yield('title', $set->nama_aplikasi) — {{ $set->nama_pendek }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { -webkit-tap-highlight-color: transparent; }
        .pb-nav { padding-bottom: calc(env(safe-area-inset-bottom) + 5rem); }
        .nav-safe { padding-bottom: env(safe-area-inset-bottom); }
        input, select, textarea { font-size: 16px; } /* cegah zoom otomatis di iPhone */
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

<header class="sticky top-0 z-20 bg-gradient-to-r from-blue-900 to-blue-700 text-white shadow">
    <div class="max-w-3xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ route('kunjungan.index') }}" class="flex items-center gap-2.5 min-w-0">
            @if($set->logo_url)
                <img src="{{ $set->logo_url }}" alt="" class="w-9 h-9 rounded-xl bg-white/90 p-1 object-contain">
            @else
                <span class="w-9 h-9 rounded-xl bg-white/15 grid place-items-center text-lg">📖</span>
            @endif
            <span class="leading-tight min-w-0">
                <span class="block text-sm font-bold truncate">{{ $set->nama_aplikasi }}</span>
                <span class="block text-[10px] text-blue-200 truncate">{{ $set->nama_pendek }}</span>
            </span>
        </a>
        <div class="flex items-center gap-2">
            <span class="hidden sm:block text-sm text-blue-100">{{ auth()->user()->name }}</span>
            <span class="w-9 h-9 rounded-full bg-white/20 grid place-items-center text-sm font-semibold">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </span>
        </div>
    </div>
</header>

<main class="max-w-3xl mx-auto px-4 pt-4 pb-nav">
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-xl text-sm">{{ $errors->first() }}</div>
    @endif
    @yield('content')
</main>

@php $isGuest = auth()->user()->role === 'guest'; @endphp
<nav class="fixed bottom-0 inset-x-0 z-20 bg-white border-t nav-safe">
    <div class="max-w-3xl mx-auto grid {{ $isGuest ? 'grid-cols-3' : 'grid-cols-2' }} text-[11px]">
        <a href="{{ route('kunjungan.index') }}"
           class="flex flex-col items-center py-2 {{ request()->routeIs('kunjungan.index', 'kunjungan.show') ? 'text-blue-700 font-semibold' : 'text-slate-500' }}">
            <span class="text-xl">🏠</span>Beranda
        </a>
        @if($isGuest)
        <a href="{{ route('kunjungan.create') }}"
           class="flex flex-col items-center py-2 {{ request()->routeIs('kunjungan.create') ? 'text-blue-700 font-semibold' : 'text-slate-500' }}">
            <span class="text-xl">➕</span>Ajukan
        </a>
        @endif
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-full flex flex-col items-center py-2 text-slate-500">
                <span class="text-xl">🚪</span>Keluar
            </button>
        </form>
    </div>
</nav>

</body>
</html>