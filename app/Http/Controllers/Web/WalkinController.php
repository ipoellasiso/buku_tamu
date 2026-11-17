<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalkinController extends Controller
{
    public function __construct(private NotificationService $notif) {}

    // ===== PUBLIK (tanpa login) =====

    public function create()
    {
        $pegawai = User::whereIn('role', ['admin', 'employee'])
            ->orderBy('name')->get(['id', 'name', 'position']);

        return view('web.walkin.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        // jebakan bot: kolom tersembunyi "website" harus kosong
        if ($request->filled('website')) {
            return redirect()->route('mulai');
        }

        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'institution' => 'nullable|string|max:120',
            'phone'       => 'nullable|string|max:20',
            'purpose'     => 'required|string|max:255',
            'purpose_type' => ['required', \Illuminate\Validation\Rule::in(\App\Support\JenisKeperluan::kode())],
            'employee_id'  => 'required_if:purpose_type,temu,konsultasi|nullable',
            // 'employee_id' => 'nullable|exists:users,id',
            'photo'       => 'nullable|image|max:4096',
        ]);

        $pegawai = !empty($data['employee_id']) ? User::find($data['employee_id']) : null;

        $guest = Guest::create([
            'name'        => $data['name'],
            'institution' => $data['institution'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'purpose'     => $data['purpose'],
            'purpose_type' => $data['purpose_type'],   // ← tambahkan ini
            'host'        => $pegawai?->name,
            'qr_token'    => (string) Str::uuid(),
            'status'      => 'pending',
            'photo_path'  => $request->hasFile('photo')
                                ? $request->file('photo')->store('guests/photos', 'public')
                                : null,
        ]);

        // beri tahu admin, dan pegawai yang dituju (kalau bukan admin)
        foreach (User::where('role', 'admin')->get() as $admin) {
            $this->notif->notify(
                $admin,
                'Tamu Langsung di Lobi',
                "{$guest->name} ingin bertemu " . ($guest->host ?? 'pegawai'),
                'walkin',
                ['guest_id' => $guest->id]
            );
        }
        if ($pegawai && $pegawai->role !== 'admin') {
            $this->notif->notify(
                $pegawai,
                'Ada Tamu Menunggu Anda',
                "{$guest->name} — {$guest->purpose}",
                'walkin',
                ['guest_id' => $guest->id]
            );
        }

        return redirect()->route('walkin.status', $guest->qr_token);
    }

    public function status($token)
    {
        $guest = Guest::where('qr_token', $token)->firstOrFail();
        return view('web.walkin.status', compact('guest'));
    }

    // ===== ADMIN / RESEPSIONIS =====

    public function index(Request $request)
    {
        $q = Guest::latest();

        if ($s = $request->search) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                ->orWhere('institution', 'like', "%{$s}%")
                ->orWhere('purpose', 'like', "%{$s}%")
                ->orWhere('host', 'like', "%{$s}%"));
        }
        if ($request->status) { $q->where('status', $request->status); }
        if ($request->dari)   { $q->whereDate('created_at', '>=', $request->dari); }
        if ($request->sampai) { $q->whereDate('created_at', '<=', $request->sampai); }

        $perPage  = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $tamu     = $q->paginate($perPage)->withQueryString();
        $menunggu = Guest::where('status', 'pending')->count();

        return view('web.walkin.index', compact('tamu', 'perPage', 'menunggu'));
    }

    // public function masuk($id)
    // {
    //     $g = Guest::findOrFail($id);
    //     if ($g->status === 'pending') {
    //         $g->update(['status' => 'confirmed', 'check_in_at' => now()]);
    //     }
    //     return back()->with('success', "{$g->name} dicatat masuk.");
    // }

    public function masuk(Request $request, $id)
    {
        $g = Guest::findOrFail($id);

        if ($g->status === 'pending') {
            $g->update(['status' => 'confirmed', 'check_in_at' => now()]);
        }

        // Kalau dipanggil via AJAX, balas JSON berisi teks pengumuman
        if ($request->expectsJson()) {
            return response()->json([
                'ok'   => true,
                'nama' => $g->name,
                'teks' => \App\Support\JenisKeperluan::kalimat($g->fresh()),
            ]);
        }

        return back()->with('success', "{$g->name} dicatat masuk.");
    }

    public function selesai($id)
    {
        $g = Guest::findOrFail($id);
        if ($g->status === 'confirmed') {
            $g->update(['status' => 'completed', 'check_out_at' => now()]);
        }
        return back()->with('success', "Kunjungan {$g->name} selesai.");
    }

    // hanya data yang masih "menunggu" (untuk bersihkan spam / salah isi)
    public function destroy($id)
    {
        $g = Guest::findOrFail($id);
        abort_unless($g->status === 'pending', 403);
        $g->delete();

        return back()->with('success', 'Data dihapus.');
    }
}