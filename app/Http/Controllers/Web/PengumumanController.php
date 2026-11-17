<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index()
    {
        return view('web.pengumuman.index');
    }

    /**
     * Feed tamu yang BARU dikonfirmasi resepsionis (check_in_at terisi).
     *
     * - Tanpa ?since => hanya mengembalikan titik awal (waktu server).
     * - Dengan ?since => tamu dengan check_in_at >= since (maksimal 10 menit ke belakang).
     */
    public function data(Request $request)
    {
        $now = now();

        if (!$request->filled('since')) {
            return response()->json(['now' => $now->timestamp, 'items' => []]);
        }

        $since = Carbon::createFromTimestamp((int) $request->query('since'));
        $batas = $now->copy()->subMinutes(10); // jangan umumkan data lama kalau perangkat baru dinyalakan
        if ($since->lt($batas)) {
            $since = $batas;
        }

        $items = Guest::whereNotNull('check_in_at')
            ->where('check_in_at', '>=', $since)
            ->orderBy('check_in_at')
            ->limit(10)
            ->get()
            ->map(fn ($t) => [
                'id'       => $t->id,
                'nama'     => $t->name,
                'instansi' => $t->institution,
                'tujuan'   => $t->host,
                'jabatan'  => $t->employee->position ?? null,
                'at'       => $t->check_in_at->timestamp,
                'jam'      => $t->check_in_at->format('H:i'),
                'teks'     => $this->kalimat($t),
            ])
            ->values();

        return response()->json(['now' => $now->timestamp, 'items' => $items]);
    }

    /** Kalimat yang diucapkan. Ubah di sini kalau mau mengganti redaksinya. */
    private function kalimat(Guest $t): string
    {
        $nama = trim((string) $t->name);
        $asal = $t->institution ? ', dari '.trim($t->institution) : '';
        $host = $t->host;

        if ($host) {
            $jabatan = $t->employee->position ?? null;
            $tujuan  = 'Bapak atau Ibu '.$host.($jabatan ? ', '.$jabatan : '');

            return "Perhatian. Tamu atas nama {$nama}{$asal}, ingin bertemu {$tujuan}. "
                 . 'Mohon segera menemui tamu di resepsionis.';
        }

        $perlu = Str::limit((string) $t->purpose, 80);

        return "Perhatian. Tamu atas nama {$nama}{$asal}, telah tiba di resepsionis. Keperluan: {$perlu}.";
    }
}