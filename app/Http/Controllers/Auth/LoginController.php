<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Saya sesuaikan dengan nama file Anda di resources/views/login.blade.php
        return view('login'); 
    }

    public function authenticate(Request $request)
        {

            $credentials = $request->validate([
                'NIP' => ['required', 'string'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                // Ambil data user yang sedang login
                $user = Auth::user();

                // Cek id_jabatan dan arahkan ke halamannya masing-masing
                // Asumsi: J01 = Kepala Kantor, J02 = Sekretaris, J03 = Kepala Bidang
                switch ($user->id_jabatan) {
                    case 'J001':
                        return redirect()->route('kepala.dashboard');
                    case 'J002':
                        return redirect()->route('kabid.dashboard');
                    case 'J003':
                        return redirect()->route('subkoor.dashboard');
                    case 'J004':
                        return redirect()->route('staff.dashboard');
                    case 'J005':
                        return redirect()->route('kepegawaian.dashboard');
                    case 'J006':
                        return redirect()->route('sekretaris.dashboard');
                    case 'J007':
                        return redirect()->route('frontliner.dashboard');            
                    default:
                        return redirect()->route('dashboard.umum'); // Default jika role tidak dikenali
                }
            }

            return back()->withErrors([
                'NIP' => 'NIP atau password yang Anda masukkan salah.',
            ])->onlyInput('NIP');
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