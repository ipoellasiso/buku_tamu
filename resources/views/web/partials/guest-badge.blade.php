{{--
    Partial badge tamu.
    Parameter:
      formId       id form yang memicu stempel saat submit
      source       id input yang jadi sumber nama (mis. 'email' atau 'nama')
      mode         'email' = nama diambil dari bagian depan email, 'name' = dipakai apa adanya
      stamp        teks stempel (default HADIR)
      status       teks status di bawah badge
      hideOnMobile true = disembunyikan di layar kecil
--}}
@php
    $stamp        = $stamp ?? 'HADIR';
    $status       = $status ?? 'Menunggu masuk';
    $mode         = $mode ?? 'email';
    $hideOnMobile = $hideOnMobile ?? false;
@endphp

<section id="guestBadge" data-form="{{ $formId }}" data-source="{{ $source }}" data-mode="{{ $mode }}"
         class="{{ $hideOnMobile ? 'hidden md:flex' : 'flex' }} flex-col items-center">
    <div class="hang flex origin-top scale-[.82] flex-col items-center md:scale-100">
        <div class="h-10 w-3 rounded-b bg-gradient-to-b from-amber to-amber/60 shadow md:h-24"></div>
        <div class="-mt-1 h-3 w-8 rounded-sm bg-white/40"></div>

        <div class="tilt-wrap">
            <div id="tilt" class="tilt">
                <div id="badge" class="badge w-72 rounded-3xl p-6 text-white">
                    <div class="mx-auto mb-5 h-2 w-16 rounded-full bg-white/30"></div>

                    <div class="flex items-center gap-4">
                        <div id="avatar" class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-amber font-display text-3xl font-extrabold text-ink">T</div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-white/70">Tamu</p>
                            <p id="guestName" class="name-swap break-words font-display text-xl font-extrabold leading-tight">Tamu Baru</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/15">
                            <p class="text-[11px] text-white/70">Tanggal</p>
                            <p id="guestDate" class="font-semibold">—</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-3 py-2 ring-1 ring-white/15">
                            <p class="text-[11px] text-white/70">Jam</p>
                            <p id="guestTime" class="font-semibold tabular-nums">—</p>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between border-t border-dashed border-white/30 pt-4">
                        <p class="text-xs text-white/70">{{ $status }}</p>
                        <span class="h-2.5 w-2.5 animate-pulse rounded-full bg-amber"></span>
                    </div>

                    <div id="stamp" class="stamp absolute bottom-14 right-4 rounded-xl border-4 border-amber bg-ink/70 px-4 py-1 font-display text-2xl font-extrabold text-amber">
                        {{ $stamp }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-6 hidden max-w-xs text-center text-sm text-white/55 md:block">
        Setiap tamu tercatat. Masuk untuk melihat siapa saja yang sudah datang hari ini.
    </p>
</section>

@push('scripts')
<script>
    (function () {
        var root   = document.getElementById('guestBadge');
        var form   = document.getElementById(root.dataset.form);
        var source = document.getElementById(root.dataset.source);
        var mode   = root.dataset.mode;
        var nameEl = document.getElementById('guestName');
        var avatar = document.getElementById('avatar');
        var stamp  = document.getElementById('stamp');
        var tilt   = document.getElementById('tilt');
        var badge  = document.getElementById('badge');

        // Tanggal & jam
        var dateEl = document.getElementById('guestDate');
        var timeEl = document.getElementById('guestTime');
        function tick() {
            var now = new Date();
            dateEl.textContent = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            timeEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
        }
        tick();
        setInterval(tick, 15000);

        // Nama mengikuti isi input
        function titleCase(s) {
            return s.split(/\s+/).slice(0, 3).map(function (w) {
                return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
            }).join(' ');
        }
        function toName(v) {
            v = (v || '').trim();
            if (mode === 'email') v = v.split('@')[0].replace(/[._\-+0-9]+/g, ' ').trim();
            return v ? titleCase(v) : 'Tamu Baru';
        }
        var last = nameEl.textContent;
        function updateName() {
            var next = toName(source.value);
            if (next === last) return;
            last = next;
            nameEl.classList.add('out');
            setTimeout(function () {
                nameEl.textContent = next;
                avatar.textContent = next.charAt(0);
                nameEl.classList.remove('out');
            }, 150);
        }
        source.addEventListener('input', updateName);
        updateName();

        // Miring 3D + kilau mengikuti kursor
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
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

        // Submit: stempel dulu, lalu kirim form
        var sent = false;
        form.addEventListener('submit', function (e) {
            if (sent || !form.checkValidity()) return;
            e.preventDefault();
            sent = true;
            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.querySelector('[data-label]').textContent = btn.dataset.loading || 'Memproses...';
            btn.querySelector('[data-spin]').classList.remove('hidden');
            stamp.classList.add('on');
            setTimeout(function () { form.submit(); }, 700);
        });
    })();
</script>
@endpush