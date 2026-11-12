<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\KunjunganGabungan;

class LaporanController extends Controller
{
    public function exportPdf(Request $request, KunjunganGabungan $svc)
    {
        $request->validate([
            'dari'   => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $dari   = Carbon::parse($request->dari)->startOfDay();
        $sampai = Carbon::parse($request->sampai)->endOfDay();
        $data   = $svc->ambil($dari, $sampai);

        $pdf = Pdf::loadView('pdf.laporan_tamu', [
            'data'    => $data,
            'stats'   => $svc->ringkas($data),
            'dari'    => $dari->format('d/m/Y'),
            'sampai'  => $sampai->format('d/m/Y'),
            'dicetak' => Carbon::now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan_tamu_' . $dari->format('Ymd') . '_' . $sampai->format('Ymd') . '.pdf');
    }

    public function previewData(Request $request, KunjunganGabungan $svc)
    {
        $request->validate([
            'dari'   => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $data  = $svc->ambil(
            Carbon::parse($request->dari)->startOfDay(),
            Carbon::parse($request->sampai)->endOfDay()
        );
        $stats = $svc->ringkas($data);

        return response()->json([
            'total'   => $stats['total'],
            'selesai' => $stats['selesai'],
            'didalam' => $stats['didalam'],
            'data'    => $data->map(fn ($r) => [
                'id'         => $r->id,
                'status'     => $r->status,
                'purpose'    => $r->keperluan,
                'guest_user' => ['name' => $r->nama],
                'employee'   => ['name' => $r->tujuan],
            ])->values(),
        ]);
    }
}
