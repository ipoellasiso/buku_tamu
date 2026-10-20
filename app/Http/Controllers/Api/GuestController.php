<?php 

// app/Http/Controllers/Api/GuestController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GuestController extends Controller
{
    // Hanya tampilkan tamu yang sudah confirmed
    public function index()
    {
        return response()->json(
            Guest::whereIn('status', ['confirmed', 'completed'])
                ->latest()
                ->get()
        );
    }

    // Tamu isi form — status pending dulu
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'purpose'   => 'required|string',
            'photo'     => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'signature' => 'nullable|file|mimes:png|max:1024',
        ]);

        $token = (string) Str::uuid();

        $data = $request->only(['name', 'institution', 'address', 'phone', 'purpose', 'host']);
        $data['qr_token']    = $token;
        $data['check_in_at'] = now();
        $data['status']      = 'pending'; // ← belum confirmed
        $data['user_id']     = $request->user()?->id;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('guests/photos', 'public');
        }

        if ($request->hasFile('signature')) {
            $data['signature_path'] = $request->file('signature')->store('guests/signatures', 'public');
        }

        // Generate QR Code
        $qrUrl  = url('/api/guests/scan/' . $token);
        $qrDir  = storage_path('app/public/guests/qrcodes');
        $qrFile = $qrDir . '/' . $token . '.svg';

        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        QrCode::format('svg')->size(300)->generate($qrUrl, $qrFile);
        $data['qr_code_path'] = 'guests/qrcodes/' . $token . '.svg';

        $guest = Guest::create($data);

        return response()->json($guest, 201);
    }

    public function show(Guest $guest)
    {
        return response()->json($guest);
    }

    // Admin scan QR → konfirmasi tamu masuk
    public function confirmCheckin($token)
    {
        $guest = Guest::where('qr_token', $token)->firstOrFail();

        if ($guest->status !== 'pending') {
            return response()->json([
                'message' => 'Tamu sudah dikonfirmasi sebelumnya',
                'guest'   => $guest,
            ]);
        }

        $guest->update(['status' => 'confirmed']);

        return response()->json([
            'message' => 'Tamu berhasil dikonfirmasi',
            'guest'   => $guest,
        ]);
    }

    // Admin selesaikan kunjungan
    public function complete(Guest $guest)
    {
        $guest->update([
            'status'       => 'completed',
            'check_out_at' => now(),
        ]);

        return response()->json([
            'message' => 'Kunjungan selesai',
            'guest'   => $guest,
        ]);
    }

    // Scan QR — return data tamu
    public function scanQr($token)
    {
        $guest = Guest::where('qr_token', $token)->firstOrFail();
        return response()->json($guest);
    }

    // Tamu lihat riwayat kunjungan sendiri
    public function myVisits(Request $request)
    {
        $visits = Guest::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return response()->json($visits);
    }

    // Checkout lama — tetap ada untuk kompatibilitas
    public function checkout(Guest $guest)
    {
        $guest->update([
            'check_out_at' => now(),
            'status'       => 'completed',
        ]);
        return response()->json(['message' => 'Check-out berhasil', 'guest' => $guest]);
    }
}