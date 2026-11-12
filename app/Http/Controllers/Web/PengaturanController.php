<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function __construct(private Pengaturan $set) {}

    public function index()
    {
        return view('web.pengaturan.index');
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama_aplikasi' => 'required|string|max:60',
            'nama_instansi' => 'required|string|max:150',
            'singkatan'     => 'nullable|string|max:30',
            'lokasi'        => 'nullable|string|max:60',
            'alamat'        => 'nullable|string|max:255',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'hapus_logo'    => 'nullable|boolean',
        ]);

        $simpan   = collect($data)->except(['logo', 'hapus_logo'])->all();
        $logoLama = $this->set->logo;

        if ($request->hasFile('logo')) {
            if ($logoLama) { Storage::disk('public')->delete($logoLama); }
            $simpan['logo'] = $request->file('logo')->store('pengaturan', 'public');
        } elseif ($request->boolean('hapus_logo')) {
            if ($logoLama) { Storage::disk('public')->delete($logoLama); }
            $simpan['logo'] = null;
        }

        $this->set->simpan($simpan);

        return back()->with('success', 'Pengaturan disimpan.');
    }

    // untuk app Flutter (publik)
    public function publik()
    {
        return response()->json([
            'nama_aplikasi' => $this->set->nama_aplikasi,
            'nama_instansi' => $this->set->nama_instansi,
            'singkatan'     => $this->set->singkatan,
            'lokasi'        => $this->set->lokasi,
            'nama_pendek'   => $this->set->nama_pendek,
            'logo_url'      => $this->set->logo_url,
        ]);
    }
}