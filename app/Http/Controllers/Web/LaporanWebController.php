<?php

namespace App\Http\Controllers\Web;

use App\Exports\LaporanTamuExport;
use App\Http\Controllers\Controller;
use App\Services\KunjunganGabungan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanWebController extends Controller
{
    public function __construct(private KunjunganGabungan $svc) {}

    private function ambil(Request $request): array
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $sampai = $request->sampai ?? Carbon::now()->format('Y-m-d');
        $sumber = in_array($request->sumber, ['terjadwal', 'langsung']) ? $request->sumber : null;

        $data = $this->svc->ambil(
            Carbon::parse($dari)->startOfDay(),
            Carbon::parse($sampai)->endOfDay(),
            null,
            $sumber
        );

        return [$data, $dari, $sampai, $sumber];
    }

    public function index(Request $request)
    {
        [$data, $dari, $sampai, $sumber] = $this->ambil($request);
        $stats = $this->svc->ringkas($data);

        return view('web.laporan.index', compact('data', 'stats', 'dari', 'sampai', 'sumber'));
    }

    public function exportPdf(Request $request)
    {
        [$data, $dari, $sampai] = $this->ambil($request);

        $pdf = Pdf::loadView('pdf.laporan_tamu', [
            'data'    => $data,
            'stats'   => $this->svc->ringkas($data),
            'dari'    => Carbon::parse($dari)->format('d/m/Y'),
            'sampai'  => Carbon::parse($sampai)->format('d/m/Y'),
            'dicetak' => Carbon::now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("laporan_tamu_{$dari}_{$sampai}.pdf");
    }

    public function exportExcel(Request $request)
    {
        [$data, $dari, $sampai] = $this->ambil($request);

        return Excel::download(new LaporanTamuExport($data), "laporan_tamu_{$dari}_{$sampai}.xlsx");
    }
}