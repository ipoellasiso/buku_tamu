<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Support\JenisKeperluan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
                'jenis'    => JenisKeperluan::label($t->purpose_type),
                'tujuan'   => $t->host,
                'at'       => $t->check_in_at->timestamp,
                'jam'      => $t->check_in_at->format('H:i'),
                // redaksi pengumuman diatur di App\Support\JenisKeperluan
                'teks'     => JenisKeperluan::kalimat($t),
            ])
            ->values();

        return response()->json(['now' => $now->timestamp, 'items' => $items]);
    }
}