<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function index()
    {
        return view('web.scan.index');
    }

    // Cari data saja, tidak mengubah status
    public function lookup(Request $request)
    {
        return response()->json($this->find($request));
    }

    // approved -> confirmed (tamu masuk)
    public function confirm(Request $request)
    {
        $visit = $this->find($request);

        if ($visit->status !== 'approved') {
            return response()->json(['message' => 'Status kunjungan bukan "Disetujui"'], 422);
        }

        $visit->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Tamu berhasil masuk',
            'visit'   => $visit->fresh(['guestUser', 'employee']),
        ]);
    }

    // confirmed -> completed (tamu selesai)
    public function complete(Request $request)
    {
        $visit = $this->find($request);

        if ($visit->status !== 'confirmed') {
            return response()->json(['message' => 'Tamu belum berstatus "Di Dalam"'], 422);
        }

        $visit->update(['status' => 'completed', 'check_out_at' => now()]);

        return response()->json([
            'message' => 'Kunjungan diselesaikan',
            'visit'   => $visit->fresh(['guestUser', 'employee']),
        ]);
    }

    // Terima token murni ATAU URL isi QR
    private function find(Request $request): VisitRequest
    {
        $request->validate(['token' => 'required|string']);

        $raw   = trim($request->token);
        $token = str_contains($raw, '/scan/') ? Str::afterLast($raw, '/scan/') : $raw;

        return VisitRequest::with(['guestUser', 'employee'])
            ->where('qr_token', $token)
            ->firstOrFail();
    }
}