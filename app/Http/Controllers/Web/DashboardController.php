<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\User;
use App\Models\VisitRequest;
use App\Services\KunjunganGabungan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(KunjunganGabungan $svc)
    {
        $today = Carbon::today();
        $dua   = fn (callable $f) => $f(VisitRequest::query()) + $f(Guest::query());

        $stats = [
            'total_hari_ini'     => $dua(fn ($q) => $q->whereDate('created_at', $today)->count()),
            'didalam'            => $dua(fn ($q) => $q->where('status', 'confirmed')->count()),
            'selesai_hari_ini'   => $dua(fn ($q) => $q->where('status', 'completed')->whereDate('check_out_at', $today)->count()),
            'bulan_ini'          => $dua(fn ($q) => $q->where('created_at', '>=', Carbon::now()->startOfMonth())->count()),
            'permohonan_pending' => VisitRequest::where('status', 'pending')->count(),
            'lobi_menunggu'      => Guest::where('status', 'pending')->count(),
            'total_pegawai'      => User::whereIn('role', ['admin', 'employee'])->count(),
            'total_tamu'         => User::where('role', 'guest')->count(),
        ];

        $tamu_terbaru = $svc->ambil(now()->subDays(30)->startOfDay())->take(5);

        $permohonan_pending = VisitRequest::with(['guestUser', 'employee'])
            ->where('status', 'pending')->latest()->take(5)->get();

        $lobi = Guest::where('status', 'pending')->latest()->take(5)->get();

        return view('web.dashboard', compact('stats', 'tamu_terbaru', 'permohonan_pending', 'lobi'));
    }
}