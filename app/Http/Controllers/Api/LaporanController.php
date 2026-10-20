<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function exportPdf(Request $request)
    {
        $request->validate([
            'dari'   => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $dari   = Carbon::parse($request->dari)->startOfDay();
        $sampai = Carbon::parse($request->sampai)->endOfDay();

        $data = VisitRequest::with(['guestUser', 'employee'])
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()
            ->get();

        $total   = $data->count();
        $selesai = $data->where('status', 'completed')->count();
        $didalam = $data->where('status', 'confirmed')->count();

        $pdf = Pdf::loadView('pdf.laporan_tamu', [
            'data'    => $data,
            'dari'    => $dari->format('d/m/Y'),
            'sampai'  => $sampai->format('d/m/Y'),
            'dicetak' => Carbon::now()->format('d/m/Y H:i'),
            'total'   => $total,
            'selesai' => $selesai,
            'didalam' => $didalam,
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_tamu_' . $dari->format('Ymd') . '_' . $sampai->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    public function previewData(Request $request)
    {
        $request->validate([
            'dari'   => 'required|date',
            'sampai' => 'required|date|after_or_equal:dari',
        ]);

        $dari   = Carbon::parse($request->dari)->startOfDay();
        $sampai = Carbon::parse($request->sampai)->endOfDay();

        $data = VisitRequest::with(['guestUser', 'employee'])
            ->whereBetween('created_at', [$dari, $sampai])
            ->latest()
            ->get();

        return response()->json([
            'total'   => $data->count(),
            'selesai' => $data->where('status', 'completed')->count(),
            'didalam' => $data->where('status', 'confirmed')->count(),
            'data'    => $data,
        ]);
    }
}
