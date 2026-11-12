<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tamu BPKAD</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; }
        .header { text-align: center; margin-bottom: 14px; }
        .header h2 { margin: 0; font-size: 15px; }
        .header h3 { margin: 2px 0 0; font-size: 12px; font-weight: normal; }
        .header p  { margin: 3px 0 0; font-size: 10px; color: #555; }
        .summary { width: 100%; margin-bottom: 12px; border-collapse: separate; border-spacing: 6px; }
        .summary td { background: #eef2ff; padding: 7px; text-align: center; width: 20%; }
        .summary b { display: block; font-size: 15px; color: #1e2a78; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #1e2a78; color: #fff; padding: 6px; text-align: left; font-size: 9px; }
        table.data td { padding: 5px 6px; border-bottom: 1px solid #ddd; font-size: 9px; vertical-align: top; }
        table.data tr:nth-child(even) td { background: #f6f7fb; }
        .footer { margin-top: 14px; text-align: right; font-size: 9px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        @php $logoFile = $set->logo ? storage_path('app/public/' . $set->logo) : null; @endphp
        @if($logoFile && is_file($logoFile))
            <img src="data:{{ mime_content_type($logoFile) }};base64,{{ base64_encode(file_get_contents($logoFile)) }}" style="height:45px"><br>
        @endif
        <h2>LAPORAN {{ mb_strtoupper($set->nama_aplikasi) }}</h2>
        <h3>{{ mb_strtoupper($set->nama_resmi) }}</h3>
        @if($set->alamat)<p>{{ $set->alamat }}</p>@endif
        <p>Periode: {{ $dari }} s/d {{ $sampai }} &nbsp;|&nbsp; Dicetak: {{ $dicetak }}</p>
    </div>

    <table class="summary">
        <tr>
            <td><b>{{ $stats['total'] }}</b>Total Kunjungan</td>
            <td><b>{{ $stats['terjadwal'] }}</b>Terjadwal</td>
            <td><b>{{ $stats['langsung'] }}</b>Tamu Langsung</td>
            <td><b>{{ $stats['selesai'] }}</b>Selesai</td>
            <td><b>{{ $stats['didalam'] }}</b>Di Dalam</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Tamu</th>
                <th>Instansi</th>
                <th>Keperluan</th>
                <th>Yang Dituju</th>
                <th>Waktu</th>
                <th>Sumber</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $v)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $v->dibuat?->format('d/m/Y') }}</td>
                <td>{{ $v->nama }}</td>
                <td>{{ $v->instansi }}</td>
                <td>{{ $v->keperluan }}</td>
                <td>{{ $v->tujuan }}</td>
                <td>{{ $v->waktu?->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ \App\Services\KunjunganGabungan::sumberLabel($v->sumber) }}</td>
                <td>{{ \App\Services\KunjunganGabungan::label($v->status) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dibuat otomatis oleh {{ $set->nama_aplikasi }} — {{ $set->nama_pendek }}</div>
</body>
</html>