<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1e2a78">
    <title>Isi Data Tamu — {{ $set->nama_pendek }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>input, select, textarea { font-size: 16px; }</style>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#1e2a78] to-blue-700 p-4 antialiased">
@php $inp = 'w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white'; @endphp

<div class="max-w-md mx-auto">
    <a href="{{ route('mulai') }}" class="text-blue-200 text-sm">← Kembali</a>

    <div class="text-center text-white my-4">
        <h1 class="text-xl font-extrabold">Data Tamu</h1>
        <p class="text-blue-200 text-sm">{{ $set->nama_pendek }}</p>
    </div>

    <div class="bg-white rounded-3xl shadow-2xl p-6">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-xl text-sm">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('walkin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- jebakan bot, jangan dihapus --}}
            <div style="position:absolute;left:-9999px" aria-hidden="true">
                <label>Website</label>
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Instansi / Asal</label>
                <input type="text" name="institution" value="{{ old('institution') }}" class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">No. HP</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Keperluan *</label>
                <textarea name="purpose" rows="2" required class="{{ $inp }}">{{ old('purpose') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Yang Dituju</label>
                <select name="employee_id" class="{{ $inp }}">
                    <option value="">— belum tahu / tanyakan resepsionis —</option>
                    @foreach($pegawai as $p)
                        <option value="{{ $p->id }}" @selected(old('employee_id') == $p->id)>
                            {{ $p->name }}{{ $p->position ? ' — '.$p->position : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Foto <span class="text-slate-400 font-normal">(opsional)</span></label>
                <input type="file" name="photo" accept="image/*" capture="user"
                       class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-blue-700 file:font-medium">
            </div>

            <button class="w-full bg-[#3f5bd9] text-white py-3.5 rounded-xl font-semibold shadow active:scale-[.98] transition">
                Kirim Data
            </button>
        </form>
    </div>
</div>
</body>
</html>