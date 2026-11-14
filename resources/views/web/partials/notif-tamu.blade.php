{{-- Badge notifikasi tamu baru (khusus admin). Di-include dari web/layouts/app.blade.php --}}
<style>
    .nt-root { position: fixed; top: 0; right: 24px; z-index: 70; pointer-events: none; }
    @media (max-width: 480px) { .nt-root { right: 12px; left: 12px; display: flex; justify-content: center; } }

    .nt-hang {
        transform-origin: 50% 0; pointer-events: auto;
        animation: ntDrop .95s cubic-bezier(.2, .9, .3, 1.2) both, ntSway 6s ease-in-out .95s infinite;
    }
    .nt-hang.out { animation: ntOut .45s ease-in forwards; }
    @keyframes ntDrop { from { transform: translateY(-130%) rotate(-6deg); opacity: 0; } to { transform: none; opacity: 1; } }
    @keyframes ntSway { 0%, 100% { transform: rotate(2deg); } 50% { transform: rotate(-2deg); } }
    @keyframes ntOut  { to { transform: translateY(-130%) rotate(5deg); opacity: 0; } }

    .nt-strap { width: 12px; height: 84px; margin: 0 auto; border-radius: 0 0 4px 4px; background: linear-gradient(#FFC247, #E0A02E); box-shadow: 0 2px 6px rgba(0,0,0,.25); }
    .nt-clip  { width: 32px; height: 12px; margin: -2px auto 0; border-radius: 3px; background: rgba(255,255,255,.6); }

    .nt-card {
        position: relative; overflow: hidden; width: 300px; max-width: calc(100vw - 24px);
        padding: 20px; border-radius: 24px; color: #fff;
        background: linear-gradient(150deg, #2F5BFF 0%, #7C5CFF 100%);
        border: 1px solid rgba(255,255,255,.28);
        box-shadow: 0 30px 60px -15px rgba(47,91,255,.7), inset 0 1px 0 rgba(255,255,255,.35);
    }
    .nt-card::after {
        content: ""; position: absolute; top: -20%; bottom: -20%; left: -60%; width: 40%;
        background: linear-gradient(100deg, transparent, rgba(255,255,255,.4), transparent);
        transform: skewX(-18deg); animation: ntShine 5s ease-in-out 1.2s infinite; pointer-events: none;
    }
    @keyframes ntShine { 0%, 60% { left: -60%; } 100% { left: 160%; } }

    .nt-pill { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: rgba(255,255,255,.16); }
    .nt-dot  { width: 8px; height: 8px; border-radius: 9999px; background: #FFC247; animation: ntPulse 1.4s ease-in-out infinite; }
    .nt-pill.permohonan .nt-dot { background: #67E8F9; }
    @keyframes ntPulse { 50% { opacity: .35; } }

    .nt-avatar { width: 52px; height: 52px; flex: none; border-radius: 16px; background: #FFC247; color: #060B24; display: grid; place-items: center; font-weight: 800; font-size: 24px; }
    .nt-name   { font-size: 18px; font-weight: 800; line-height: 1.2; word-break: break-word; }
    .nt-sub    { font-size: 12px; color: rgba(255,255,255,.7); }
    .nt-rows   { margin-top: 14px; display: grid; gap: 8px; font-size: 13px; }
    .nt-row    { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); border-radius: 12px; padding: 8px 12px; }
    .nt-row small { display: block; font-size: 11px; color: rgba(255,255,255,.7); }
    .nt-row b { font-weight: 600; word-break: break-word; }

    .nt-actions { display: flex; gap: 8px; margin-top: 14px; }
    .nt-btn { flex: 1; text-align: center; border-radius: 12px; padding: 9px 12px; font-size: 14px; font-weight: 600; cursor: pointer; border: 0; text-decoration: none; }
    .nt-btn.primary { background: #fff; color: #1e2a78; }
    .nt-btn.ghost   { background: rgba(255,255,255,.14); color: #fff; }
    .nt-btn:focus-visible { outline: 3px solid rgba(255,255,255,.6); outline-offset: 2px; }

    .nt-more { margin-top: 10px; text-align: center; font-size: 12px; color: rgba(255,255,255,.75); }
    .nt-bar  { position: absolute; left: 0; bottom: 0; height: 3px; width: 100%; background: #FFC247; transform-origin: left; animation: ntBar 15s linear forwards; }
    .nt-card:hover .nt-bar { animation-play-state: paused; }
    @keyframes ntBar { to { transform: scaleX(0); } }

    @media (prefers-reduced-motion: reduce) {
        .nt-hang { animation: ntDrop .01s both; }
        .nt-card::after, .nt-dot { animation: none; }
    }
</style>

<div id="ntRoot" class="nt-root" aria-live="polite"></div>

<script>
    (function () {
        var FEED = @json(route('notif.tamu'));
        var KEY  = 'nt_last_v1';
        var root = document.getElementById('ntRoot');
        var queue = [], showing = false, polling = false, ctx = null;

        function load() { try { return JSON.parse(localStorage.getItem(KEY)); } catch (e) { return null; } }
        function save(v) { try { localStorage.setItem(KEY, JSON.stringify(v)); } catch (e) {} }

        // Nada singkat saat badge muncul (diam saja kalau browser belum mengizinkan suara)
        function ding() {
            try {
                var AC = window.AudioContext || window.webkitAudioContext;
                if (!AC) return;
                ctx = ctx || new AC();
                if (ctx.state === 'suspended') ctx.resume();
                [880, 1320].forEach(function (f, i) {
                    var o = ctx.createOscillator(), g = ctx.createGain(), t = ctx.currentTime + i * 0.14;
                    o.type = 'sine'; o.frequency.value = f;
                    g.gain.setValueAtTime(0.0001, t);
                    g.gain.exponentialRampToValueAtTime(0.12, t + 0.02);
                    g.gain.exponentialRampToValueAtTime(0.0001, t + 0.3);
                    o.connect(g); g.connect(ctx.destination);
                    o.start(t); o.stop(t + 0.32);
                });
            } catch (e) {}
        }

        function el(tag, cls, text) {
            var n = document.createElement(tag);
            if (cls) n.className = cls;
            if (text !== undefined) n.textContent = text;
            return n;
        }
        function row(label, value) {
            var r = el('div', 'nt-row');
            r.appendChild(el('small', '', label));
            r.appendChild(el('b', '', value || '-'));
            return r;
        }

        function render(it) {
            root.innerHTML = '';
            var hang = el('div', 'nt-hang');
            hang.appendChild(el('div', 'nt-strap'));
            hang.appendChild(el('div', 'nt-clip'));

            var card = el('div', 'nt-card');
            card.setAttribute('role', 'status');

            var pill = el('span', 'nt-pill ' + it.tipe);
            pill.appendChild(el('span', 'nt-dot'));
            pill.appendChild(document.createTextNode(it.tipe === 'langsung' ? 'Tamu langsung baru' : 'Permohonan baru'));
            card.appendChild(pill);

            var head = el('div', '');
            head.style.cssText = 'display:flex;align-items:center;gap:12px;margin-top:14px';
            head.appendChild(el('div', 'nt-avatar', (it.nama || 'T').charAt(0).toUpperCase()));
            var who = el('div', '');
            who.appendChild(el('div', 'nt-name', it.nama));
            if (it.instansi) who.appendChild(el('div', 'nt-sub', it.instansi));
            head.appendChild(who);
            card.appendChild(head);

            var rows = el('div', 'nt-rows');
            rows.appendChild(row('Keperluan', it.keperluan));
            rows.appendChild(row('Yang dituju', it.tujuan));
            rows.appendChild(row(it.waktu_label, it.waktu));
            card.appendChild(rows);

            var act = el('div', 'nt-actions');
            var a = el('a', 'nt-btn primary', 'Lihat');
            a.href = it.url;
            var b = el('button', 'nt-btn ghost', 'Tutup');
            b.type = 'button';
            b.addEventListener('click', close);
            act.appendChild(a); act.appendChild(b);
            card.appendChild(act);

            if (queue.length) card.appendChild(el('div', 'nt-more', '+' + queue.length + ' tamu lagi menunggu'));

            var bar = el('div', 'nt-bar');
            bar.addEventListener('animationend', close);
            card.appendChild(bar);

            hang.appendChild(card);
            root.appendChild(hang);
        }

        function next() {
            if (showing || !queue.length) return;
            showing = true;
            var it = queue.shift();
            render(it);
            ding();
        }

        function close() {
            var h = root.querySelector('.nt-hang');
            if (!h || h.classList.contains('out')) return;
            h.classList.add('out');
            setTimeout(function () {
                root.innerHTML = '';
                showing = false;
                setTimeout(next, 250);
            }, 450);
        }

        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

        function poll() {
            if (polling) return;
            polling = true;
            var last = load();
            var url = FEED + (last ? '?g=' + last.g + '&v=' + last.v : '');
            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (d) {
                    if (!d) return;
                    if (d.last) save(d.last);
                    if (d.items && d.items.length) {
                        d.items.forEach(function (i) { queue.push(i); });
                        next();
                    }
                })
                .catch(function () {})
                .then(function () { polling = false; });
        }

        poll();
        setInterval(function () { if (!document.hidden) poll(); }, 8000);
        document.addEventListener('visibilitychange', function () { if (!document.hidden) poll(); });
    })();
</script>