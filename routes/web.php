<?php

use Illuminate\Support\Facades\Route;
use function Laravel\Prompts\title;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/frontliner', function () {
    return view('dashboardFr',
    ['title' => 'Frontliner',
    'role' => 'Frontliner']);
});

Route::get('/frontliner/input_surat', function () {
    return view('inputSurat', 
    ['title' => 'Frontliner',
    'role' => 'Frontliner']);
});

Route::get('/frontliner/riwayat_input', function () {
    return view('RiwayatInput', 
    ['title' => 'Frontliner',
    'role' => 'Frontliner']);
});

Route::get('/frontliner/kalender_kantor', function () {
    return view('KalenderKantor',
    ['title' => 'Frontliner',
    'role' => 'Frontliner']);
});

Route::get('/frontliner/profil', function () {
    return view('Profil', 
    ['title' => 'Frontliner',
    'role' => 'Frontliner']);
});

Route::get('/kepala', function () {
    return view('dashboardKepala', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/kepala/surat_masuk', function () {
    return view('suratMasuk&Dispo', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/kepala/agenda', function () {
    return view('agenda', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/kepala/Laporan_Pemantauan', function () {
    return view('laporanPemantauan', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/kepala/kalender_kantor', function () {
    return view('KalenderKantor', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/kepala/profil', function () {
    return view('profil', 
    ['title' => 'Kepala',
    'role' => 'Kepala']);
});

Route::get('/', function () {
    return redirect('/login');
});

// Rute khusus tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');
});

// Rute khusus yang sudah login (auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Rute sementara untuk dashboard
    Route::get('/dashboard', function () {
        return "Selamat datang di Dashboard!";
    })->name('dashboard');
});