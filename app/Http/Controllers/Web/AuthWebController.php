<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    public function showLogin()    { return view('web.auth.login'); }
    public function showRegister() { return view('web.auth.register'); }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($cred)) {
            return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
        }

        $request->session()->regenerate();
        return redirect()->intended($this->home(Auth::user()));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'guest',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // return redirect()->route('kunjungan.index');
        return redirect()->route('kunjungan.create')
            ->with('success', 'Akun berhasil dibuat. Silakan isi data kunjungan Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function home(User $user): string
    {
        return $user->role === 'admin'
            ? route('dashboard')
            : route('kunjungan.index');
    }
}