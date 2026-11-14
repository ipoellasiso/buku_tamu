<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\VisitRequest;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Feed tamu baru untuk badge notifikasi di panel admin.
     *
     * - Tanpa ?g & ?v  => hanya mengembalikan titik awal (id terakhir), tanpa item.
     * - Dengan ?g & ?v => mengembalikan item yang id-nya lebih besar dari itu.
     */
    public function tamuBaru(Request $request)
    {
        // id terakhir dihitung dulu, supaya data yang masuk di tengah proses tidak terlewat
        $maxG = (int) Guest::max('id');
        $maxV = (int) VisitRequest::max('id');

        $payload = [
            'last'    => ['g' => $maxG, 'v' => $maxV],
            'pending' => [
                'langsung'   => Guest::where('status', 'pending')->count(),
                'permohonan' => VisitRequest::where('status', 'pending')->count(),
            ],
            'items'   => [],
        ];

        if (!$request->has('g') || !$request->has('v')) {
            return response()->json($payload);
        }

        $g     = (int) $request->query('g');
        $v     = (int) $request->query('v');
        $batas = now()->subHours(6); // abaikan data lama kalau admin baru buka lagi setelah lama
        $items = collect();

        // 1) Tamu langsung
        Guest::where('id', '>', $g)->where('id', '<=', $maxG)
            ->where('status', 'pending')
            ->where('created_at', '>=', $batas)
            ->orderBy('id')->limit(5)->get()
            ->each(function ($t) use ($items) {
                $items->push([
                    'key'       => 'g'.$t->id,
                    'tipe'      => 'langsung',
                    'nama'      => $t->name ?: 'Tamu',
                    'instansi'  => $t->institution ?: '',
                    'keperluan' => (string) $t->purpose,
                    'tujuan'    => $t->host ?: 'Belum ditentukan',
                    'waktu_label' => 'Datang',
                    'waktu'     => optional($t->created_at)->format('d/m/Y H:i'),
                    'url'       => route('walkin.index'),
                    'at'        => optional($t->created_at)->timestamp,
                ]);
            });

        // 2) Permohonan (web maupun Flutter, sama-sama masuk tabel visit_requests)
        VisitRequest::where('id', '>', $v)->where('id', '<=', $maxV)
            ->where('status', 'pending')
            ->where('created_at', '>=', $batas)
            ->orderBy('id')->limit(5)->get()
            ->each(function ($r) use ($items) {
                $jadwal = $r->scheduled_at ?? $r->requested_at ?? $r->created_at;
                $items->push([
                    'key'       => 'v'.$r->id,
                    'tipe'      => 'permohonan',
                    'nama'      => $r->user->name ?? $r->guest_name ?? $r->name ?? 'Tamu',
                    'instansi'  => $r->institution ?? '',
                    'keperluan' => (string) ($r->purpose ?? ''),
                    'tujuan'    => $r->employee->name ?? 'Belum ditentukan',
                    'waktu_label' => 'Jadwal',
                    'waktu'     => $jadwal ? \Illuminate\Support\Carbon::parse($jadwal)->format('d/m/Y H:i') : '-',
                    'url'       => route('permohonan.show', $r->id),
                    'at'        => optional($r->created_at)->timestamp,
                ]);
            });

        $payload['items'] = $items->sortBy('at')->values()->all();

        return response()->json($payload);
    }
}