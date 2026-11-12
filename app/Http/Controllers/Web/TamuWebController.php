<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\KunjunganGabungan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TamuWebController extends Controller
{
    public function __construct(private KunjunganGabungan $svc) {}

    // Daftar tamu yang sudah masuk (di dalam / selesai), dari kedua jalur
    public function index(Request $request)
    {
        $status = in_array($request->status, ['confirmed', 'completed'])
            ? [$request->status]
            : ['confirmed', 'completed'];

        $sumber = in_array($request->sumber, ['terjadwal', 'langsung']) ? $request->sumber : null;

        $data = $this->svc->ambil(
            $request->dari   ? Carbon::parse($request->dari)->startOfDay() : null,
            $request->sampai ? Carbon::parse($request->sampai)->endOfDay() : null,
            $status,
            $sumber,
            $request->search
        );

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $tamu    = $this->svc->paginate($data, $perPage, $request);

        return view('web.tamu.index', compact('tamu', 'perPage'));
    }

    public function show(string $sumber, int $id)
    {
        $tamu = $this->svc->satu($sumber, $id);
        return view('web.tamu.show', compact('tamu'));
    }
}