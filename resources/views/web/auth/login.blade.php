<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — {{ $set->nama_aplikasi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-900 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
    <div class="text-center mb-8">
        @if($set->logo_url)
            <img src="{{ $set->logo_url }}" alt="Logo" class="h-16 mx-auto mb-3 object-contain">
        @endif
        <h1 class="text-2xl font-bold text-gray-800">{{ $set->nama_aplikasi }}</h1>
        <p class="text-gray-500 text-sm">{{ $set->nama_pendek }}</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button class="w-full bg-blue-800 text-white py-2 rounded-lg font-semibold hover:bg-blue-900">MASUK</button>
    </form>

    <p class="text-center text-sm mt-4 text-gray-600">
        Belum punya akun? <a href="{{ route('register') }}" class="text-blue-700 font-semibold">Daftar</a>
    </p>
</div>
</body>
</html>