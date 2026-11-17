@extends('web.layouts.app')
@section('title', 'Pengumuman Suara')
@section('header', 'Pengumuman Suara')
@section('crumb', 'Pengaturan')
@section('subtitle', 'Setelah resepsionis menekan Masuk, suara otomatis menyebut tamu dan pegawai yang dituju')

@section('content')
<div class="grid lg:grid-cols-5 gap-5">

    <div class="card p-6 lg:col-span-3 space-y-6">

        {{-- status --}}
        <div class="flex items-center gap-4">
            <span id="dot" class="relative flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-amber-100 text-2xl">
                <span id="dotPing" class="absolute inset-0 hidden animate-ping rounded-full bg-emerald-400/40"></span>
                <span id="dotIco" class="relative">🔇</span>
            </span>
            <div class="min-w-0">
                <p id="stTitle" class="font-bold text-slate-800 text-lg">Suara belum aktif</p>
                <p id="stSub" class="text-sm text-slate-500">Klik tombol di samping sekali setelah halaman dibuka. Browser mewajibkan ini sebelum boleh bersuara.</p>
            </div>
            <button id="btnOn" type="button" class="btn-primary ml-auto shrink-0">🔊 Aktifkan suara</button>
        </div>

        <p id="noVoice" class="hidden rounded-xl bg-orange-50 border border-orange-200 text-orange-800 text-sm p-3">
            Perangkat ini belum punya suara Bahasa Indonesia, jadi ucapan bisa terdengar kurang pas.
            Gunakan Chrome atau Edge, atau pasang suara Indonesia di pengaturan Speech sistem operasi.
        </p>

        {{-- pengaturan --}}
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="lbl" for="voiceSel">Suara</label>
                <select id="voiceSel" class="inp"></select>
            </div>
            <div>
                <label class="lbl" for="rate">Kecepatan <span id="rateVal" class="normal-case font-normal"></span></label>
                <input id="rate" type="range" min="0.6" max="1.3" step="0.05" value="0.9" class="w-full">
            </div>
            <div>
                <label class="lbl" for="vol">Volume <span id="volVal" class="normal-case font-normal"></span></label>
                <input id="vol" type="range" min="0.2" max="1" step="0.05" value="1" class="w-full">
            </div>
            <div>
                <label class="lbl" for="rep">Diulang</label>
                <select id="rep" class="inp">
                    <option value="1">1 kali</option>
                    <option value="2">2 kali</option>
                    <option value="3">3 kali</option>
                </select>
            </div>
            <div class="flex items-end">
                <button id="btnTest" type="button" class="btn-outline w-full">▶ Tes suara</button>
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
            <p class="font-semibold text-slate-700 mb-1">Cara pakai</p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Buka halaman ini di komputer yang tersambung ke speaker, lalu klik <b>Aktifkan suara</b>.</li>
                <li>Biarkan tab tetap terbuka. Jangan di-minimize, karena browser memperlambat tab yang tersembunyi.</li>
                <li>Kalau halaman dimuat ulang, klik <b>Aktifkan suara</b> lagi.</li>
            </ul>
        </div>
    </div>

    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-3">
            <p class="font-semibold text-slate-700">Pengumuman terakhir</p>
            <span id="conn" class="text-xs text-slate-400">menghubungkan…</span>
        </div>
        <p id="logEmpty" class="text-sm text-slate-400 py-8 text-center">Belum ada pengumuman sejak halaman dibuka.</p>
        <ul id="log" class="space-y-2"></ul>
    </div>
</div>

{{-- <script>
    (function () {
        var DATA = @json(route('pengumuman.data'));
        var K_SINCE = 'png_since', K_DONE = 'png_done', K_SET = 'png_set';

        function $(id) { return document.getElementById(id); }
        function ld(k) { try { return JSON.parse(localStorage.getItem(k)); } catch (e) { return null; } }
        function sv(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }

        var synth = window.speechSynthesis;
        var enabled = false, busy = false, queue = [], ctx = null, wake = null;
        var since = ld(K_SINCE);
        var doneList = ld(K_DONE) || [];
        var done = {};
        doneList.forEach(function (id) { done[id] = 1; });

        if (!synth) {
            $('stTitle').textContent = 'Browser tidak mendukung suara';
            $('stSub').textContent = 'Gunakan Chrome atau Edge versi terbaru.';
            $('btnOn').disabled = true;
            return;
        }

        // ===== pengaturan tersimpan =====
        var set = ld(K_SET) || {};
        if (set.rate) $('rate').value = set.rate;
        if (set.vol)  $('vol').value = set.vol;
        if (set.rep)  $('rep').value = set.rep;
        function saveSet() {
            set = { voice: $('voiceSel').value, rate: $('rate').value, vol: $('vol').value, rep: $('rep').value };
            sv(K_SET, set);
            $('rateVal').textContent = '(' + $('rate').value + 'x)';
            $('volVal').textContent = '(' + Math.round($('vol').value * 100) + '%)';
        }
        ['rate', 'vol', 'rep', 'voiceSel'].forEach(function (id) { $(id).addEventListener('change', saveSet); });
        $('rate').addEventListener('input', saveSet);
        $('vol').addEventListener('input', saveSet);

        // ===== daftar suara =====
        var voices = [];
        function loadVoices() {
            voices = synth.getVoices();
            var id = voices.filter(function (v) { return /^id/i.test(v.lang); });
            var list = id.length ? id : voices;
            var sel = $('voiceSel');
            sel.innerHTML = '';
            list.forEach(function (v) {
                var o = document.createElement('option');
                o.value = v.voiceURI;
                o.textContent = v.name + ' (' + v.lang + ')';
                sel.appendChild(o);
            });
            if (set.voice) sel.value = set.voice;
            $('noVoice').classList.toggle('hidden', id.length > 0);
            saveSet();
        }
        synth.onvoiceschanged = loadVoices;
        loadVoices();

        function pickVoice() {
            var uri = $('voiceSel').value;
            for (var i = 0; i < voices.length; i++) if (voices[i].voiceURI === uri) return voices[i];
            return null;
        }

        // ===== suara =====
        function say(text, cb) {
            var u = new SpeechSynthesisUtterance(text), called = false;
            var v = pickVoice();
            u.lang = 'id-ID';
            if (v) { u.voice = v; u.lang = v.lang; }
            u.rate = parseFloat($('rate').value);
            u.volume = parseFloat($('vol').value);
            function fin() { if (!called) { called = true; if (cb) cb(); } }
            u.onend = fin; u.onerror = fin;
            synth.speak(u);
        }

        // nada "ding-dong" sebelum pengumuman
        function chime(cb) {
            try {
                var AC = window.AudioContext || window.webkitAudioContext;
                ctx = ctx || new AC();
                if (ctx.state === 'suspended') ctx.resume();
                var vol = parseFloat($('vol').value);
                [659, 523].forEach(function (f, i) {
                    var o = ctx.createOscillator(), g = ctx.createGain(), t = ctx.currentTime + i * 0.45;
                    o.type = 'sine'; o.frequency.value = f;
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(0.25 * vol + 0.0001, t + 0.03);
                    g.gain.exponentialRampToValueAtTime(0.0001, t + 0.6);
                    o.connect(g); g.connect(ctx.destination);
                    o.start(t); o.stop(t + 0.65);
                });
                setTimeout(cb, 1100);
            } catch (e) { cb(); }
        }

        function pump() {
            if (!enabled || busy || !queue.length) return;
            busy = true;
            var it = queue.shift();
            chime(function () {
                var n = parseInt($('rep').value, 10) || 1, i = 0;
                (function loop() {
                    if (i >= n) { busy = false; setTimeout(pump, 800); return; }
                    i++;
                    say(it.teks, function () { setTimeout(loop, 900); });
                })();
            });
        }

        // ===== log =====
        function addLog(it) {
            $('logEmpty').classList.add('hidden');
            var li = document.createElement('li');
            li.className = 'rounded-xl border border-slate-200 p-3 text-sm';

            var top = document.createElement('div');
            top.className = 'flex items-center justify-between gap-2';
            var who = document.createElement('p');
            who.className = 'font-semibold text-slate-800 min-w-0 truncate';
            who.textContent = it.nama;
            var jam = document.createElement('span');
            jam.className = 'text-xs text-slate-400 shrink-0';
            jam.textContent = it.jam;
            top.appendChild(who); top.appendChild(jam);

            var to = document.createElement('p');
            to.className = 'text-slate-500';
            to.textContent = (it.jenis ? it.jenis + ' · ' : '') + (it.tujuan ? 'Menemui ' + it.tujuan : 'Tujuan ditentukan resepsionis');

            var again = document.createElement('button');
            again.type = 'button';
            again.className = 'mt-2 text-xs font-medium text-[#3f5bd9] hover:underline';
            again.textContent = '↻ Ulangi';
            again.addEventListener('click', function () { queue.push(it); pump(); });

            li.appendChild(top); li.appendChild(to); li.appendChild(again);
            var log = $('log');
            log.insertBefore(li, log.firstChild);
            while (log.children.length > 20) log.removeChild(log.lastChild);
        }

        // ===== polling =====
        function poll() {
            var url = DATA + (since ? '?since=' + since : '');
            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (d) {
                    if (!d) { $('conn').textContent = 'terputus'; return; }
                    $('conn').textContent = 'terhubung';
                    if (!since) { since = d.now; sv(K_SINCE, since); return; }
                    d.items.forEach(function (it) {
                        if (done[it.id]) return;
                        done[it.id] = 1; doneList.push(it.id);
                        if (it.at > since) since = it.at;
                        queue.push(it); addLog(it);
                    });
                    doneList = doneList.slice(-60);
                    sv(K_DONE, doneList); sv(K_SINCE, since);
                    pump();
                })
                .catch(function () { $('conn').textContent = 'terputus'; });
        }
        poll();
        setInterval(poll, 4000);

        // ===== tombol =====
        function paint() {
            $('dot').className = 'relative flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-2xl ' + (enabled ? 'bg-emerald-100' : 'bg-amber-100');
            $('dotPing').classList.toggle('hidden', !enabled);
            $('dotIco').textContent = enabled ? '🔊' : '🔇';
            $('stTitle').textContent = enabled ? 'Suara aktif' : 'Suara belum aktif';
            $('stSub').textContent = enabled
                ? 'Siap mengumumkan tamu yang dikonfirmasi resepsionis. Biarkan halaman ini tetap terbuka.'
                : 'Klik tombol di samping sekali setelah halaman dibuka. Browser mewajibkan ini sebelum boleh bersuara.';
            $('btnOn').textContent = enabled ? '🔇 Matikan suara' : '🔊 Aktifkan suara';
        }

        function keepAwake() {
            if (!enabled || !('wakeLock' in navigator)) return;
            navigator.wakeLock.request('screen').then(function (l) { wake = l; }).catch(function () {});
        }
        document.addEventListener('visibilitychange', function () { if (!document.hidden) keepAwake(); });

        $('btnOn').addEventListener('click', function () {
            enabled = !enabled;
            paint();
            if (enabled) {
                synth.cancel();
                say('Pengumuman suara aktif.');
                keepAwake();
                setTimeout(pump, 1500);
            } else {
                synth.cancel();
                queue = []; busy = false;
                if (wake) { wake.release(); wake = null; }
            }
        });

        $('btnTest').addEventListener('click', function () {
            synth.cancel();
            chime(function () {
                say('Perhatian. Tamu atas nama Budi, dari Dinas Contoh, ingin bertemu Bapak atau Ibu Anggi Ermawan, Kepala Bidang. Mohon segera menemui tamu di resepsionis.');
            });
        });

        paint();
    })();
</script> --}}

<script>
    (function () {
        var DATA = @json(route('pengumuman.data'));
        var K_SINCE = 'png_since', K_DONE = 'png_done', K_SET = 'png_set',
            K_ON = 'png_on', K_LOG = 'png_log';

        function $(id) { return document.getElementById(id); }
        function ld(k) { try { return JSON.parse(localStorage.getItem(k)); } catch (e) { return null; } }
        function sv(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} }
        function rm(k) { try { localStorage.removeItem(k); } catch (e) {} }

        var synth = window.speechSynthesis;
        var enabled = false, busy = false, queue = [], ctx = null, wake = null;
        var wantOn = ld(K_ON) === true;           // pernah diaktifkan?
        var since = ld(K_SINCE);
        var doneList = ld(K_DONE) || [];
        var done = {};
        doneList.forEach(function (id) { done[id] = 1; });

        // guard log supaya item yang sama tidak muncul dobel di panel kanan
        var loggedList = ld(K_LOG) || [];
        var logged = {};
        loggedList.forEach(function (id) { logged[id] = 1; });

        if (!synth) {
            $('stTitle').textContent = 'Browser tidak mendukung suara';
            $('stSub').textContent = 'Gunakan Chrome atau Edge versi terbaru.';
            $('btnOn').disabled = true;
            return;
        }

        // ===== pengaturan tersimpan =====
        var set = ld(K_SET) || {};
        if (set.rate) $('rate').value = set.rate;
        if (set.vol)  $('vol').value = set.vol;
        if (set.rep)  $('rep').value = set.rep;
        function saveSet() {
            set = { voice: $('voiceSel').value, rate: $('rate').value, vol: $('vol').value, rep: $('rep').value };
            sv(K_SET, set);
            $('rateVal').textContent = '(' + $('rate').value + 'x)';
            $('volVal').textContent = '(' + Math.round($('vol').value * 100) + '%)';
        }
        ['rate', 'vol', 'rep', 'voiceSel'].forEach(function (id) { $(id).addEventListener('change', saveSet); });
        $('rate').addEventListener('input', saveSet);
        $('vol').addEventListener('input', saveSet);

        // ===== daftar suara =====
        var voices = [];
        function loadVoices() {
            voices = synth.getVoices();
            var id = voices.filter(function (v) { return /^id/i.test(v.lang); });
            var list = id.length ? id : voices;
            var sel = $('voiceSel');
            sel.innerHTML = '';
            list.forEach(function (v) {
                var o = document.createElement('option');
                o.value = v.voiceURI;
                o.textContent = v.name + ' (' + v.lang + ')';
                sel.appendChild(o);
            });
            if (set.voice) sel.value = set.voice;
            $('noVoice').classList.toggle('hidden', id.length > 0);
            saveSet();
        }
        synth.onvoiceschanged = loadVoices;
        loadVoices();

        function pickVoice() {
            var uri = $('voiceSel').value;
            for (var i = 0; i < voices.length; i++) if (voices[i].voiceURI === uri) return voices[i];
            return null;
        }

        // ===== suara =====
        function say(text, cb) {
            var u = new SpeechSynthesisUtterance(text), called = false;
            var v = pickVoice();
            u.lang = 'id-ID';
            if (v) { u.voice = v; u.lang = v.lang; }
            u.rate = parseFloat($('rate').value);
            u.volume = parseFloat($('vol').value);
            function fin() { if (!called) { called = true; if (cb) cb(); } }
            u.onend = fin; u.onerror = fin;
            synth.speak(u);
        }

        // nada "ding-dong" sebelum pengumuman
        function chime(cb) {
            try {
                var AC = window.AudioContext || window.webkitAudioContext;
                ctx = ctx || new AC();
                if (ctx.state === 'suspended') ctx.resume();
                var vol = parseFloat($('vol').value);
                [659, 523].forEach(function (f, i) {
                    var o = ctx.createOscillator(), g = ctx.createGain(), t = ctx.currentTime + i * 0.45;
                    o.type = 'sine'; o.frequency.value = f;
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(0.25 * vol + 0.0001, t + 0.03);
                    g.gain.exponentialRampToValueAtTime(0.0001, t + 0.6);
                    o.connect(g); g.connect(ctx.destination);
                    o.start(t); o.stop(t + 0.65);
                });
                setTimeout(cb, 1100);
            } catch (e) { cb(); }
        }

        function pump() {
            if (!enabled || busy || !queue.length) return;
            busy = true;
            var it = queue.shift();
            chime(function () {
                var n = parseInt($('rep').value, 10) || 1, i = 0;
                (function loop() {
                    if (i >= n) { busy = false; setTimeout(pump, 800); return; }
                    i++;
                    say(it.teks, function () { setTimeout(loop, 900); });
                })();
            });
        }

        // ===== log =====
        function addLog(it) {
            if (logged[it.id]) return;        // sudah pernah tampil → jangan dobel
            logged[it.id] = 1; loggedList.push(it.id);
            loggedList = loggedList.slice(-60); sv(K_LOG, loggedList);

            $('logEmpty').classList.add('hidden');
            var li = document.createElement('li');
            li.className = 'rounded-xl border border-slate-200 p-3 text-sm';

            var top = document.createElement('div');
            top.className = 'flex items-center justify-between gap-2';
            var who = document.createElement('p');
            who.className = 'font-semibold text-slate-800 min-w-0 truncate';
            who.textContent = it.nama;
            var jam = document.createElement('span');
            jam.className = 'text-xs text-slate-400 shrink-0';
            jam.textContent = it.jam;
            top.appendChild(who); top.appendChild(jam);

            var to = document.createElement('p');
            to.className = 'text-slate-500';
            to.textContent = (it.jenis ? it.jenis + ' · ' : '') + (it.tujuan ? 'Menemui ' + it.tujuan : 'Tujuan ditentukan resepsionis');

            var again = document.createElement('button');
            again.type = 'button';
            again.className = 'mt-2 text-xs font-medium text-[#3f5bd9] hover:underline';
            again.textContent = '↻ Ulangi';
            again.addEventListener('click', function () { queue.push(it); pump(); });

            li.appendChild(top); li.appendChild(to); li.appendChild(again);
            var log = $('log');
            log.insertBefore(li, log.firstChild);
            while (log.children.length > 20) log.removeChild(log.lastChild);
        }

        // ===== polling =====
        function poll() {
            var url = DATA + (since ? '?since=' + since : '');
            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (d) {
                    if (!d) { $('conn').textContent = 'terputus'; return; }
                    $('conn').textContent = 'terhubung';

                    // request pertama → hanya jadikan titik awal
                    if (!since) { since = d.now; sv(K_SINCE, since); return; }

                    d.items.forEach(function (it) {
                        if (done[it.id]) return;

                        // Suara belum aktif → JANGAN konsumsi item.
                        // Hanya tampilkan di log. since & done TIDAK dimajukan,
                        // sehingga saat user aktifkan suara, poll berikutnya
                        // akan mengambil item ini lagi dan langsung dibunyikan.
                        if (!enabled) { addLog(it); return; }

                        done[it.id] = 1; doneList.push(it.id);
                        if (it.at > since) since = it.at;
                        queue.push(it); addLog(it);
                    });

                    // simpan progres HANYA saat enabled, biar item lama tidak hilang
                    if (enabled) {
                        doneList = doneList.slice(-60);
                        sv(K_DONE, doneList); sv(K_SINCE, since);
                    }
                    pump();
                })
                .catch(function () { $('conn').textContent = 'terputus'; });
        }
        poll();
        setInterval(poll, 4000);

        // ===== tombol & status =====
        function paint() {
            $('dot').className = 'relative flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-2xl ' + (enabled ? 'bg-emerald-100' : 'bg-amber-100');
            $('dotPing').classList.toggle('hidden', !enabled);
            $('dotIco').textContent = enabled ? '🔊' : '🔇';
            $('stTitle').textContent = enabled ? 'Suara aktif' : 'Suara belum aktif';
            $('stSub').textContent = enabled
                ? 'Siap mengumumkan tamu yang dikonfirmasi resepsionis. Biarkan halaman ini tetap terbuka.'
                : 'Klik tombol di samping sekali setelah halaman dibuka. Browser mewajibkan ini sebelum boleh bersuara.';
            $('btnOn').textContent = enabled ? '🔇 Matikan suara' : '🔊 Aktifkan suara';
        }

        function keepAwake() {
            if (!enabled || !('wakeLock' in navigator)) return;
            navigator.wakeLock.request('screen').then(function (l) { wake = l; }).catch(function () {});
        }
        document.addEventListener('visibilitychange', function () { if (!document.hidden) keepAwake(); });

        function enable() {
            if (enabled) return;
            enabled = true;
            sv(K_ON, true);
            paint();
            try { synth.cancel(); say('Pengumuman suara aktif.'); } catch (e) {}
            keepAwake();
            setTimeout(pump, 1500);
        }

        function disable() {
            if (!enabled) return;
            enabled = false;
            sv(K_ON, false);
            paint();
            try { synth.cancel(); } catch (e) {}
            queue = []; busy = false;
            if (wake) { wake.release(); wake = null; }
        }

        $('btnOn').addEventListener('click', function () {
            enabled ? disable() : enable();
        });

        $('btnTest').addEventListener('click', function () {
            synth.cancel();
            chime(function () {
                say('Perhatian. Tamu atas nama Budi, dari Dinas Contoh, ingin bertemu Bapak atau Ibu Anggi Ermawan, Kepala Bidang. Mohon segera menemui tamu di resepsionis.');
            });
        });

        paint();

        // ===== auto-restore: kalau sebelumnya aktif, minta 1 interaksi lalu nyalakan =====
        if (wantOn && !enabled) {
            $('stTitle').textContent = 'Klik di mana saja untuk mengaktifkan suara';
            $('stSub').textContent = 'Suara sudah pernah diaktifkan. Browser butuh satu klik setelah reload.';

            var unlock = function () {
                document.removeEventListener('pointerdown', unlock);
                document.removeEventListener('keydown', unlock);
                // "buka" izin audio dengan utterance kosong
                try { synth.cancel(); synth.speak(new SpeechSynthesisUtterance(' ')); } catch (e) {}
                enable();
            };
            document.addEventListener('pointerdown', unlock);
            document.addEventListener('keydown', unlock);
        }
    })();
</script>
@endsection