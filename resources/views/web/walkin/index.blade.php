@extends('web.layouts.app')
@section('title', 'Tamu Langsung')
@section('header', 'Tamu Langsung')
@section('crumb', 'Kunjungan')
@section('subtitle', 'Tamu yang mengisi data sendiri di kantor (tanpa akun)')

@section('content')
@if($menunggu > 0)
<div class="mb-5 p-4 rounded-xl bg-orange-50 border border-orange-200 text-orange-800 text-sm">
    ⏳ <b>{{ $menunggu }}</b> tamu menunggu di lobi.
</div>
@endif

<form method="GET" class="card p-4 mb-5">
    <div class="flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-64">
            <label class="lbl">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="inp" placeholder="Nama, instansi, keperluan...">
        </div>
        <div>
            <label class="lbl">Tanggal dari</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="inp">
        </div>
        <div>
            <label class="lbl">Tanggal sampai</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="inp">
        </div>
        <div>
            <label class="lbl">Status</label>
            <select name="status" class="inp">
                <option value="">— Semua —</option>
                @foreach(['pending' => 'Menunggu', 'confirmed' => 'Di Dalam', 'completed' => 'Selesai'] as $v => $l)
                    <option value="{{ $v }}" @selected(request('status') === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary">🔍 Tampilkan</button>
        <a href="{{ route('walkin.index') }}" class="btn-outline">↺ Reset</a>
    </div>
</form>

<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
        <h3 class="text-sm font-semibold text-slate-600">☰ Daftar Tamu Langsung</h3>
        <span class="text-xs font-semibold bg-indigo-50 text-[#3f5bd9] rounded-full px-3 py-1">{{ $tamu->total() }}</span>
    </div>

    <form method="GET" class="px-5 pt-4 flex items-center gap-2 text-sm text-slate-500">
        @foreach(request()->except('per_page', 'page') as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        Tampilkan
        <select name="per_page" onchange="this.form.submit()" class="inp !w-auto !py-1">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
            @endforeach
        </select>
        data
    </form>

    <div class="overflow-x-auto p-5">
        <table class="text-sm border border-slate-200 rounded-lg">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Tamu</th>
                    <th class="px-4 py-3 text-left">Yang Dituju</th>
                    <th class="px-4 py-3 text-left">Keperluan</th>
                    <th class="px-4 py-3 text-left">Waktu</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tamu as $g)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500">{{ $tamu->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($g->photo_path)
                                <img src="{{ asset('storage/'.$g->photo_path) }}" class="w-10 h-10 rounded-full object-cover" alt="">
                            @else
                                <span class="w-10 h-10 rounded-full bg-slate-100 grid place-items-center text-slate-400">👤</span>
                            @endif
                            <div>
                                <p class="font-medium text-slate-800">{{ $g->name }}</p>
                                <p class="text-xs text-slate-400">{{ $g->institution ?: '-' }}{{ $g->phone ? ' · '.$g->phone : '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $g->host ?? '-' }}</td>
                    <td class="px-4 py-3">
                        {{ \Illuminate\Support\Str::limit($g->purpose, 32) }}
                        <p class="text-xs text-slate-400">{{ \App\Support\JenisKeperluan::label($g->purpose_type) }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        <p>Datang: {{ $g->created_at->format('d/m/Y H:i') }}</p>
                        @if($g->check_in_at)<p>Masuk: {{ $g->check_in_at->format('H:i') }}</p>@endif
                        @if($g->check_out_at)<p>Selesai: {{ $g->check_out_at->format('H:i') }}</p>@endif
                    </td>
                    <td class="px-4 py-3">@include('web.partials.status', ['status' => $g->status])</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            @if($g->status === 'pending')
                                <button type="button"
                                        class="btn-green !px-3 !py-1.5 !text-xs js-masuk"
                                        data-id="{{ $g->id }}"
                                        data-url="{{ route('walkin.masuk', $g->id) }}"
                                        data-csrf="{{ csrf_token() }}">Masuk
                                </button>
                                <form action="{{ route('walkin.destroy', $g->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus data tamu ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-outline !px-3 !py-1.5 !text-xs !text-red-600 !border-red-300">Hapus</button>
                                </form>
                            @elseif($g->status === 'confirmed')
                                <form action="{{ route('walkin.selesai', $g->id) }}" method="POST">
                                    @csrf
                                    <button class="btn-primary !px-3 !py-1.5 !text-xs">Selesai</button>
                                </form>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">Belum ada tamu langsung</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 pb-5">{{ $tamu->links() }}</div>
</div>

{{-- segarkan otomatis tiap 30 detik, selama tidak sedang mengetik --}}
<script>
    setTimeout(function () {
        const a = document.activeElement;
        const speaking = window.speechSynthesis && window.speechSynthesis.speaking;
        if ((!a || a === document.body) && !speaking) location.reload();
    }, 30000);
</script>

<audio id="ttsPlayer" preload="auto"></audio>

<script>
(function () {
    var player = document.getElementById('ttsPlayer');
    var unlocked = false;

    function unlockAll() {
        if (unlocked) return;
        try {
            player.src = 'data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4LjI5LjEwMAAAAAAAAAAAAAAA//tQxAADB8AhSmxhIIEVCSiA=';
            player.play().then(function () {
                player.pause(); player.currentTime = 0;
                player.removeAttribute('src');
            }).catch(function () {});
        } catch (e) {}
        unlocked = true;
        console.log('[TTS] unlocked');
    }
    document.addEventListener('pointerdown', unlockAll, { once: true });
    document.addEventListener('keydown', unlockAll, { once: true });

    function speak(teks, done) {
        console.log('[TTS] speak:', teks);
        if (!('speechSynthesis' in window)) { if (done) done(); return; }
        speechSynthesis.cancel();

        var u = new SpeechSynthesisUtterance(teks);
        u.lang = 'id-ID';
        u.rate = 0.9;
        u.volume = 1;

        var finished = false;
        function fin() {
            if (finished) return;
            finished = true;
            console.log('[TTS] selesai');
            if (done) done();
        }
        u.onend = fin;
        u.onerror = function (e) { console.warn('[TTS] error:', e.error); fin(); };

        setTimeout(function () {
            speechSynthesis.speak(u);
            console.log('[TTS] mulai bicara');
            setTimeout(fin, 30000); // watchdog
        }, 100);
    }

    document.querySelectorAll('.js-masuk').forEach(function (btn) {
        btn.addEventListener('click', function () {
            unlockAll();
            btn.disabled = true;
            btn.textContent = '...';
            console.log('[Masuk] POST', btn.dataset.url);

            fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': btn.dataset.csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
            .then(function (r) {
                console.log('[Masuk] status', r.status);
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (d) {
                console.log('[Masuk] response', d);
                if (d && d.ok && d.teks) {
                    speak(d.teks, function () {
                        location.reload();   // ← reload SETELAH suara selesai
                    });
                } else {
                    setTimeout(function () { location.reload(); }, 500);
                }
            })
            .catch(function (err) {
                console.error('[Masuk] gagal:', err);
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = btn.dataset.url;
                var c = document.createElement('input');
                c.type = 'hidden'; c.name = '_token'; c.value = btn.dataset.csrf;
                f.appendChild(c);
                document.body.appendChild(f);
                f.submit();
            });
        });
    });
})();
</script>
@endsection