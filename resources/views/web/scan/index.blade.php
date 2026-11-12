@extends('web.layouts.app')
@section('title', 'Scan QR')
@section('header', 'Scan QR Tamu')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="font-semibold mb-3">Kamera</h3>
        <div id="reader" class="w-full"></div>
        <p id="camMsg" class="text-xs text-gray-500 mt-2"></p>

        <label class="block text-sm font-medium text-gray-700 mt-4 mb-1">Atau tempel token / isi QR</label>
        <div class="flex gap-2">
            <input id="manual" class="flex-1 border rounded-lg px-3 py-2 text-sm" placeholder="token atau URL QR">
            <button onclick="lookup(document.getElementById('manual').value)"
                    class="px-4 py-2 bg-blue-700 text-white rounded-lg text-sm">Cari</button>
        </div>
    </div>

    <div id="result" class="bg-white rounded-xl shadow p-6">
        <p class="text-gray-500 text-sm">Scan QR tamu untuk melihat datanya.</p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
const CSRF = '{{ csrf_token() }}';
const URL_LOOKUP   = '{{ route('scan.lookup') }}';
const URL_CONFIRM  = '{{ route('scan.confirm') }}';
const URL_COMPLETE = '{{ route('scan.complete') }}';

const LABEL = {pending:'Menunggu', approved:'Disetujui', confirmed:'Di Dalam', completed:'Selesai', rejected:'Ditolak'};
let currentToken = null, busy = false, last = '', lastAt = 0;

const esc = s => String(s ?? '-').replace(/[&<>"']/g,
    c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

async function post(url, token) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({token})
    });
    return {ok: res.ok, json: await res.json().catch(() => ({}))};
}

function show(html) { document.getElementById('result').innerHTML = html; }

function render(v, msg) {
    let action = '<p class="text-sm text-gray-500 mt-4">Tidak ada aksi untuk status ini.</p>';
    if (v.status === 'approved')
        action = `<button onclick="act(URL_CONFIRM)" class="mt-4 w-full bg-blue-700 text-white py-2 rounded-lg font-semibold">Konfirmasi Masuk</button>`;
    if (v.status === 'confirmed')
        action = `<button onclick="act(URL_COMPLETE)" class="mt-4 w-full bg-green-600 text-white py-2 rounded-lg font-semibold">Selesaikan Kunjungan</button>`;

    show(`
        ${msg ? `<div class="mb-3 p-3 bg-green-100 text-green-800 rounded-lg text-sm">${esc(msg)}</div>` : ''}
        <h3 class="font-semibold mb-3">${esc(v.guest_user?.name)}
            <span class="ml-2 text-xs px-2 py-1 rounded-full bg-gray-100">${esc(LABEL[v.status] ?? v.status)}</span></h3>
        <dl class="space-y-1 text-sm">
            <div><span class="text-gray-500">Keperluan:</span> ${esc(v.purpose)}</div>
            <div><span class="text-gray-500">Yang dituju:</span> ${esc(v.employee?.name)}</div>
            <div><span class="text-gray-500">Jadwal:</span> ${esc(v.scheduled_at)}</div>
        </dl>
        ${action}`);
}

function error(m) {
    show(`<div class="p-3 bg-red-100 text-red-700 rounded-lg text-sm">${esc(m)}</div>`);
}

async function lookup(raw) {
    raw = (raw || '').trim();
    if (!raw || busy) return;
    busy = true;
    const {ok, json} = await post(URL_LOOKUP, raw);
    busy = false;
    if (!ok) return error('QR tidak ditemukan');
    currentToken = json.qr_token;
    render(json);
}

async function act(url) {
    const {ok, json} = await post(url, currentToken);
    if (!ok) return error(json.message || 'Gagal memproses');
    currentToken = json.visit.qr_token;
    render(json.visit, json.message);
}

if (window.isSecureContext) {
    new Html5Qrcode('reader')
        .start({facingMode: 'environment'}, {fps: 10, qrbox: 250}, text => {
            const now = Date.now();
            if (text === last && now - lastAt < 3000) return;
            last = text; lastAt = now;
            lookup(text);
        })
        .catch(e => document.getElementById('camMsg').textContent = 'Kamera tidak bisa dibuka: ' + e);
} else {
    document.getElementById('camMsg').textContent =
        'Kamera browser hanya jalan di HTTPS atau localhost. Pakai input manual, atau buka lewat http://localhost:8000 di laptop.';
}
</script>
@endsection