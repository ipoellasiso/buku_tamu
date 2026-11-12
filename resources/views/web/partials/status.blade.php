@php
    $map = [
        'pending'   => ['Menunggu',  'bg-orange-100 text-orange-700'],
        'approved'  => ['Disetujui', 'bg-blue-100 text-blue-700'],
        'confirmed' => ['Di Dalam',  'bg-teal-100 text-teal-700'],
        'completed' => ['Selesai',   'bg-green-100 text-green-700'],
        'rejected'  => ['Ditolak',   'bg-red-100 text-red-700'],
    ];
    [$label, $cls] = $map[$status] ?? [$status, 'bg-gray-100 text-gray-700'];
@endphp
<span class="px-2 py-1 rounded-full text-xs {{ $cls }}">{{ $label }}</span>