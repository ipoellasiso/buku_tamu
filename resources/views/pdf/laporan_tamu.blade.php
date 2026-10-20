<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tamu BPKAD</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 4px 0; font-size: 12px; }
        .info { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #1565C0; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:nth-child(even) { background-color: #f5f5f5; }
        .footer { margin-top: 20px; text-align: right; font-size: 11px; color: #666; }
        .summary { margin-bottom: 16px; padding: 10px; background: #f0f4ff; border-radius: 4px; }
        .summary span { margin-right: 20px; }
        .status-confirmed  { color: #1565C0; font-weight: bold; }
        .status-completed  { color: #2e7d32; font-weight: bold; }
        .status-pending    { color: #e65100; font-weight: bold; }
        .status-approved   { color: #0277bd; font-weight: bold; }
        .status-rejected   { color: #c62828; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BUKU TAMU</h2>
        <h3>BPKAD KOTA PALU</h3>
        <p>Periode: {{ $dari }} s/d {{ $sampai }}</p>
        <p>Dicetak pada: {{ $dicetak }}</p>
    </div>

    <div class="summary">
        <strong>Ringkasan:</strong><br>
        <span>Total Tamu: <strong>{{ $total }}</strong></span>
        <span>Selesai: <strong>{{ $selesai }}</strong></span>
        <span>Di Dalam: <strong>{{ $didalam }}</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tamu</th>
                <th>Instansi</th>
                <th>Keperluan</th>
                <th>Yang Dituju</th>
                <th>Jadwal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $v)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $v->guestUser?->name ?? $v->name ?? '-' }}</td>
                <td>{{ $v->guestUser?->institution ?? '-' }}</td>
                <td>{{ $v->purpose }}</td>
                <td>{{ $v->employee?->name ?? $v->host ?? '-' }}</td>
                <td>{{ $v->scheduled_at ? \Carbon\Carbon::parse($v->scheduled_at)->format('d/m/Y H:i') : '-' }}</td>
                <td class="status-{{ $v->status }}">
                    @switch($v->status)
                        @case('approved')  Disetujui @break
                        @case('confirmed') Di Dalam  @break
                        @case('completed') Selesai   @break
                        @case('rejected')  Ditolak   @break
                        @default           Menunggu
                    @endswitch
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan ini digenerate otomatis oleh Sistem Buku Tamu BPKAD Kota Palu
    </div>
</body>
</html>