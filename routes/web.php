<?php

use Illuminate\Support\Facades\Route;
use function Laravel\Prompts\title;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect('/login');
});

// === RUTE KHUSUS TAMU (Belum Login) ===
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

// === RUTE KHUSUS PEGAWAI (Sudah Login) ===
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --------------------------------------------------------
    // 1. RUTE KEPALA KANTOR (Role: J01)
    // --------------------------------------------------------
    Route::middleware('role:J001')->group(function () {
        Route::get('/kepala', function () {
            return view('dashboardKepala', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.dashboard');

        Route::get('/kepala/surat_masuk', function () {
            return view('suratMasuk&Dispo', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.surat_masuk');

        Route::get('/kepala/agenda', function () {
            return view('agenda', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.agenda');

        Route::get('/kepala/Laporan_Pemantauan', function () {
            return view('laporanPemantauan', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.laporan');

        Route::get('/kepala/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.kalender');

        Route::get('/kepala/profil', function () {
            return view('profil', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.profil');
    });


    Route::middleware('role:J003')->group(function () {
        Route::get('/kabid', function () {
            return view('dashboardKepala', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.dashboard');

        Route::get('/kabid/surat_masuk', function () {
            return view('suratMasuk&Dispo', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.surat_masuk');

        Route::get('/kabid/agenda', function () {
            return view('agenda', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.agenda');

        Route::get('/kabid/Laporan_Pemantauan', function () {
            return view('laporanPemantauan', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.laporan');

        Route::get('/kabid/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.kalender');

        Route::get('/kabid/profil', function () {
            return view('profil', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.profil');
    });


    // --------------------------------------------------------
    // 2. RUTE FRONTLINER (Role: J04)
    // --------------------------------------------------------
    Route::middleware('role:J007')->group(function () {
        Route::get('/frontliner', function () {
            return view('dashboardFr', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.dashboard');

        Route::get('/frontliner/input_surat', function () {
            return view('inputSurat', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.input_surat');

        Route::get('/frontliner/riwayat_input', function () {
            return view('RiwayatInput', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.riwayat_input');

        Route::get('/frontliner/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.kalender');

        Route::get('/frontliner/profil', function () {
            return view('Profil', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.profil');
    });

});