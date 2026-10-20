<?php
// app/Http/Controllers/Api/VisitRequestController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitRequestController extends Controller
{
    public function __construct(private NotificationService $notif) {}

    // Tamu buat permohonan
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'purpose'      => 'required|string',
            'notes'        => 'nullable|string',
            'requested_at' => 'required|date|after:now',
        ]);

        $visit = VisitRequest::create([
            'guest_user_id' => $request->user()->id,
            'employee_id'   => $request->employee_id,
            'purpose'       => $request->purpose,
            'notes'         => $request->notes,
            'requested_at'  => $request->requested_at,
            'status'        => 'pending',
        ]);

        $visit->load(['guestUser', 'employee']);

        // Notif ke admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notif->notify(
                $admin,
                'Permohonan Kunjungan Baru',
                "{$visit->guestUser->name} ingin bertemu {$visit->employee->name}",
                'visit_request',
                ['visit_request_id' => $visit->id]
            );
        }

        return response()->json($visit, 201);
    }

    // Admin approve + set jadwal + generate QR
    public function approve(Request $request, VisitRequest $visitRequest)
    {
        $request->validate([
            'scheduled_at'   => 'required|date',
            'employee_notes' => 'nullable|string',
        ]);

        // Generate QR Token
        $token  = (string) Str::uuid();
        $qrUrl  = url('/api/visit-requests/scan/' . $token);
        $qrDir  = storage_path('app/public/visit-requests/qrcodes');
        $qrFile = $qrDir . '/' . $token . '.svg';

        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        QrCode::format('svg')->size(300)->generate($qrUrl, $qrFile);

        $visitRequest->update([
            'status'         => 'approved',
            'scheduled_at'   => $request->scheduled_at,
            'employee_notes' => $request->employee_notes,
            'qr_token'       => $token,
            'qr_code_path'   => 'visit-requests/qrcodes/' . $token . '.svg',
        ]);

        $visitRequest->load(['guestUser', 'employee']);

        // Notif ke tamu
        $this->notif->notify(
            $visitRequest->guestUser,
            'Kunjungan Disetujui ✅',
            "Kunjungan Anda dengan {$visitRequest->employee->name} disetujui. Jadwal: {$request->scheduled_at}",
            'visit_approved',
            ['visit_request_id' => $visitRequest->id]
        );

        // Notif ke pegawai yang dituju
        $this->notif->notify(
            $visitRequest->employee,
            'Jadwal Tamu Dikonfirmasi',
            "{$visitRequest->guestUser->name} akan berkunjung pada {$request->scheduled_at}",
            'visit_approved',
            ['visit_request_id' => $visitRequest->id]
        );

        return response()->json($visitRequest);
    }

    // Admin reject
    public function reject(Request $request, VisitRequest $visitRequest)
    {
        $request->validate([
            'employee_notes' => 'nullable|string',
        ]);

        $visitRequest->update([
            'status'         => 'rejected',
            'employee_notes' => $request->employee_notes,
        ]);

        $visitRequest->load(['guestUser', 'employee']);

        // Notif ke tamu
        $this->notif->notify(
            $visitRequest->guestUser,
            'Kunjungan Ditolak ❌',
            "Maaf, kunjungan Anda dengan {$visitRequest->employee->name} tidak dapat diterima. {$request->employee_notes}",
            'visit_rejected',
            ['visit_request_id' => $visitRequest->id]
        );

        return response()->json($visitRequest);
    }

    // Admin scan QR → tamu masuk (confirmed)
    public function scanQr($token)
    {
        $visit = VisitRequest::where('qr_token', $token)
            ->with(['guestUser', 'employee'])
            ->firstOrFail();

        if ($visit->status === 'approved') {
            $visit->update(['status' => 'confirmed']);
        }

        return response()->json([
            'message' => 'Tamu berhasil masuk',
            'visit'   => $visit,
        ]);
    }

    // Admin selesaikan kunjungan
    public function complete(VisitRequest $visitRequest)
    {
        $visitRequest->update([
            'status'       => 'completed',
            'check_out_at' => now(),
        ]);

        $visitRequest->load(['guestUser', 'employee']);

        // Notif ke tamu
        $this->notif->notify(
            $visitRequest->guestUser,
            'Kunjungan Selesai',
            'Terima kasih telah berkunjung ke BPKAD Kota Palu.',
            'visit_completed',
            ['visit_request_id' => $visitRequest->id]
        );

        return response()->json($visitRequest);
    }

    // List permohonan
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = VisitRequest::with(['guestUser', 'employee'])->latest();

        if ($user->role === 'guest') {
            $query->where('guest_user_id', $user->id);
        } elseif ($user->role === 'employee') {
            $query->where('employee_id', $user->id);
        }
        // admin lihat semua

        return response()->json($query->get());
    }

    public function show(VisitRequest $visitRequest)
    {
        return response()->json(
            $visitRequest->load(['guestUser', 'employee'])
        );
    }

    // Ambil data by token tanpa update status
    public function getByToken($token)
    {
        $visit = VisitRequest::where('qr_token', $token)
            ->with(['guestUser', 'employee'])
            ->firstOrFail();

        return response()->json(['visit' => $visit]);
    }

    // scanQr — update status jadi confirmed
    // public function scanQr($token)
    // {
    //     $visit = VisitRequest::where('qr_token', $token)
    //         ->with(['guestUser', 'employee'])
    //         ->firstOrFail();

    //     if ($visit->status === 'approved') {
    //         $visit->update(['status' => 'confirmed']);
    //     }

    //     return response()->json(['visit' => $visit]);
    // }

}