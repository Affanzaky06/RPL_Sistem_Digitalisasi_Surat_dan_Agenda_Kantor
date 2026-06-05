<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('login');
    }

    // Memproses data nip dan password yang dikirim dari form
    public function authenticate(Request $request)
    {
        // Validasi inputan harus diisi
        $credentials = $request->validate([
            'nip' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Coba cocokkan nip dan password ke database
        if (Auth::attempt($credentials)) {
            // Jika berhasil, perbarui sesi agar aman
            $request->session()->regenerate();

            // Arahkan ke halaman dashboard
            return redirect()->intended('dashboard');
        }

        // Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'nip' => 'NIP atau password yang Anda masukkan salah.',
        ])->onlyInput('nip');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}