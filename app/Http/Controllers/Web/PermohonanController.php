<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PermohonanController extends Controller
{
    public function __construct(private NotificationService $notif) {}

    public function index(Request $request)
    {
        $q = VisitRequest::with(['guestUser', 'employee'])->latest();

        if ($s = $request->search) {
            $q->where(function ($w) use ($s) {
                $w->where('purpose', 'like', "%{$s}%")
                ->orWhereHas('guestUser', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                ->orWhereHas('employee',  fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }
        if ($request->status) { $q->where('status', $request->status); }
        if ($request->dari)   { $q->whereDate('created_at', '>=', $request->dari); }
        if ($request->sampai) { $q->whereDate('created_at', '<=', $request->sampai); }

        $perPage    = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $permohonan = $q->paginate($perPage)->withQueryString();

        return view('web.permohonan.index', compact('permohonan', 'perPage'));
    }

    public function show($id)
    {
        $permohonan = VisitRequest::with(['guestUser', 'employee'])->findOrFail($id);
        return view('web.permohonan.show', compact('permohonan'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['scheduled_at' => 'required|date']);

        $visit = VisitRequest::findOrFail($id);

        $token  = (string) Str::uuid();
        $qrUrl  = url('/api/visit-requests/scan/' . $token);
        $qrDir  = storage_path('app/public/visit-requests/qrcodes');
        $qrFile = $qrDir . '/' . $token . '.svg';
        $jadwal = \Carbon\Carbon::parse($request->scheduled_at)->format('d/m/Y H:i');

        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        QrCode::format('svg')->size(300)->generate($qrUrl, $qrFile);

        $visit->update([
            'status'         => 'approved',
            'scheduled_at'   => $request->scheduled_at,
            'employee_notes' => $request->employee_notes,
            'qr_token'       => $token,
            'qr_code_path'   => 'visit-requests/qrcodes/' . $token . '.svg',
        ]);

        $visit->load(['guestUser', 'employee']);

        $this->notif->notify(
            $visit->guestUser,
            'Kunjungan Disetujui ✅',
            "Kunjungan Anda dengan {$visit->employee->name} disetujui. Jadwal: {$jadwal}",
            'visit_approved',
            ['visit_request_id' => $visit->id]
        );

        $this->notif->notify(
            $visit->guestUser,
            'Kunjungan Disetujui ✅',
            "Kunjungan Anda dengan {$visit->employee->name} disetujui. Jadwal: {$jadwal}",
            'visit_approved',
            ['visit_request_id' => $visit->id]
        );

        return back()->with('success', 'Permohonan disetujui dan QR berhasil dibuat.');
    }

    public function reject(Request $request, $id)
    {
        $visit = VisitRequest::findOrFail($id);
        $visit->update([
            'status'         => 'rejected',
            'employee_notes' => $request->employee_notes,
        ]);

        $visit->load(['guestUser', 'employee']);

        $this->notif->notify(
            $visit->guestUser,
            'Kunjungan Ditolak ❌',
            "Maaf, kunjungan Anda ditolak. {$request->employee_notes}",
            'visit_rejected',
            ['visit_request_id' => $visit->id]
        );

        return back()->with('success', 'Permohonan ditolak.');
    }

    public function complete($id)
    {
        VisitRequest::findOrFail($id)->update([
            'status'       => 'completed',
            'check_out_at' => now(),
        ]);

        return back()->with('success', 'Kunjungan diselesaikan.');
    }
}