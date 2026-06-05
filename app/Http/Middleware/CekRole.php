<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika belum login, tendang ke halaman login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Jika id_jabatan sesuai dengan role yang diizinkan, persilakan masuk
        if (Auth::user()->id_jabatan == $role) {
            return $next($request);
        }

        // Jika tidak sesuai, kembalikan ke halaman sebelumnya atau beri error 403 (Terlarang)
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
