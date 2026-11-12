<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\VisitRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KunjunganController extends Controller
{
    public function __construct(private NotificationService $notif) {}

    public function index()
    {
        $user = Auth::user();

        $base = VisitRequest::query();
        if ($user->role === 'guest') {
            $base->where('guest_user_id', $user->id);
        } elseif ($user->role === 'employee') {
            $base->where('employee_id', $user->id);
        }

        $ringkasan = [
            'menunggu' => (clone $base)->where('status', 'pending')->count(),
            'aktif'    => (clone $base)->whereIn('status', ['approved', 'confirmed'])->count(),
            'selesai'  => (clone $base)->where('status', 'completed')->count(),
        ];

        $kunjungan  = (clone $base)->with(['guestUser', 'employee'])->latest()->paginate(10);
        $notifikasi = Notification::where('user_id', $user->id)->latest()->take(5)->get();

        return view('web.kunjungan.index', compact('kunjungan', 'notifikasi', 'ringkasan'));
    }

    public function create()
    {
        abort_unless(Auth::user()->role === 'guest', 403);

        $pegawai = User::whereIn('role', ['admin', 'employee'])
            ->orderBy('name')
            ->get(['id', 'name', 'position', 'department']);

        return view('web.kunjungan.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'guest', 403);

        $data = $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'purpose'      => 'required|string|max:255',
            'notes'        => 'nullable|string',
            'requested_at' => 'required|date|after:now',
        ]);

        $visit = VisitRequest::create($data + [
            'guest_user_id' => Auth::id(),
            'status'        => 'pending',
        ]);
        $visit->load(['guestUser', 'employee']);

        foreach (User::where('role', 'admin')->get() as $admin) {
            $this->notif->notify(
                $admin,
                'Permohonan Kunjungan Baru',
                "{$visit->guestUser->name} ingin bertemu {$visit->employee->name}",
                'visit_request',
                ['visit_request_id' => $visit->id]
            );
        }

        return redirect()->route('kunjungan.show', $visit->id)
            ->with('success', 'Permohonan terkirim. Tunggu persetujuan admin.');
    }

    public function show($id)
    {
        $user      = Auth::user();
        $kunjungan = VisitRequest::with(['guestUser', 'employee'])->findOrFail($id);

        abort_unless(
            $user->role === 'admin'
            || $kunjungan->guest_user_id === $user->id
            || $kunjungan->employee_id === $user->id,
            403
        );

        return view('web.kunjungan.show', compact('kunjungan'));
    }
}