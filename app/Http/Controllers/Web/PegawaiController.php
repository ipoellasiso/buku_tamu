<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::whereIn('role', ['admin', 'employee'])->latest()->paginate(15);
        return view('web.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('web.pegawai.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'role'       => 'required|in:admin,employee',
            'position'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:20',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pegawai = User::findOrFail($id);
        return view('web.pegawai.form', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = User::findOrFail($id);

        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $id,
            'role'       => 'required|in:admin,employee',
            'position'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'nullable|min:6',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai diperbarui.');
    }

    public function destroy($id)
    {
        abort_if((int) $id === auth()->id(), 403, 'Tidak bisa menghapus akun sendiri');
        User::findOrFail($id)->delete();

        return back()->with('success', 'Pegawai dihapus.');
    }
}