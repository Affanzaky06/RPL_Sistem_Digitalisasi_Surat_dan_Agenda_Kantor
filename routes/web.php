<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontliner\SuratController;
use App\Http\Controllers\KalenderKantorController;
use App\Http\Controllers\KepalaKantor\DashboardKepalaController;
use App\Http\Controllers\KepalaKantor\DisposisiKepalaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Sekretaris\DashboardSekretarisController;
use App\Http\Controllers\Sekretaris\VerifikasiController;
use Illuminate\Support\Facades\Route;

use function Laravel\Prompts\title;

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
    // 1. RUTE KEPALA KANTOR (Role: J001)
    // --------------------------------------------------------
    Route::middleware('role:J001')->group(function () {
        Route::get(
            '/kepala',
            [DashboardKepalaController::class, 'index']
        )->name('kepala.dashboard');

        Route::get(
            '/kepala/surat_masuk',
            [DisposisiKepalaController::class, 'index']
        )->name('kepala.surat_masuk');

        Route::get(
            '/kepala/agenda',
            [AgendaController::class, 'index']
            )->name('kepala.agenda');

        Route::get('/kepala/Laporan_Pemantauan', function () {
            return view('laporanPemantauan', ['title' => 'Kepala', 'role' => 'Kepala']);
        })->name('kepala.laporan');

       Route::get(
        '/kepala/kalender_kantor', 
        [KalenderKantorController::class, 'index']
        )->name('kepala.kalender');

        Route::get('/kepala/profil',
         [ProfilController::class, 'index']
         )->name('kepala.profil');
    });

    // --------------------------------------------------------
    // 2. RUTE KABID (Role: J002)
    // --------------------------------------------------------

    Route::middleware('role:J002')->group(function () {
        Route::get(
            '/kabid',
            [DashboardKepalaController::class, 'index']
        )->name('kabid.dashboard');

        Route::get(
            '/kabid/surat_masuk',
            [DisposisiKepalaController::class, 'index']
        )->name('kabid.surat_masuk');

        Route::get('/kabid/agenda', 
            [agendaController::class, 'index']
        )->name('kabid.agenda');

        Route::get('/kabid/Laporan_Pemantauan', function () {
            return view('laporanPemantauan', ['title' => 'Kabid', 'role' => 'Kabid']);
        })->name('kabid.laporan');

        Route::get(
            '/kabid/kalender_kantor', 
            [KalenderKantorController::class, 'index']
        )->name('kabid.kalender');

        Route::get('/kabid/profil',
        [ProfilController::class, 'index']
        )->name('kabid.profil');
    });

    // --------------------------------------------------------
    // 3. RUTE SUBKOOR (Role: J003)
    // --------------------------------------------------------

    Route::middleware('role:J003')->group(function () {
        Route::get(
            '/subkoor',
            [DashboardKepalaController::class, 'index']
        )->name('subkoor.dashboard');

       Route::get(
            '/subkoor/surat_masuk',
            [DisposisiKepalaController::class, 'index']
        )->name('subkoor.surat_masuk');

        Route::get('/subkoor/agenda', 
        [agendaController::class, 'index']
        )->name('subkoor.agenda');

        Route::get('/subkoor/Laporan_Pemantauan', function () {
            return view('laporanPemantauan', ['title' => 'Subkoor', 'role' => 'Subkoor']);
        })->name('subkoor.laporan');

         Route::get(
            '/subkoor/kalender_kantor', 
            [KalenderKantorController::class, 'index']
        )->name('kabid.kalender');

        Route::get('/subkoor/profil',
        [ProfilController::class, 'index']
        )->name('subkoor.profil');
    });


    // --------------------------------------------------------
    // 4. RUTE STAF (Role: J004)
    // --------------------------------------------------------

    Route::middleware('role:J004')->group(function () {
        Route::get('/staff', function () {
            return view('dashboardKepala', ['title' => 'Staff', 'role' => 'Staff']);
        })->name('staff.dashboard');

        Route::get('/staff/surat_masuk', function () {
            return view('suratMasuk', ['title' => 'Staff', 'role' => 'Staff']);
        })->name('staff.surat_masuk');

        Route::get('/staff/agenda', function () {
            return view('agenda', ['title' => 'Staff', 'role' => 'Staff']);
        })->name('staff.agenda');

        Route::get('/staff/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Staff', 'role' => 'Staff']);
        })->name('staff.kalender');

        Route::get('/staff/profil', function () {
            return view('profil', ['title' => 'Staff', 'role' => 'Staff']);
        })->name('staff.profil');
    });

    // --------------------------------------------------------
    // 5. RUTE KEPEGAWAIAN (Role: J005)
    // --------------------------------------------------------



    // --------------------------------------------------------
    // 6. RUTE SEKRETARIS (Role: J006)
    // --------------------------------------------------------

    Route::middleware('role:J006')->group(function () {


        Route::get(
            '/sekretaris',
            [DashboardSekretarisController::class, 'index']
        )->name('sekretaris.dashboard');

        Route::get(
            '/sekretaris/verifikasi_surat',
            [VerifikasiController::class, 'index']
        )->name('sekretaris.verifikasi_surat');

        Route::put(
            '/sekretaris/verifikasi/{id}',
            [VerifikasiController::class, 'verifikasi']
        )->name('sekretaris.verifikasi');

        Route::put(
            '/sekretaris/tolak/{id}',
            [VerifikasiController::class, 'tolak']
        )->name('sekretaris.tolak');

        Route::get('/sekretaris/agenda', function () {
            return view('agenda', ['title' => 'Sekretaris', 'role' => 'Sekretaris']);
        })->name('sekretaris.agenda');

        Route::get('/sekretaris/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Sekretaris', 'role' => 'Sekretaris']);
        })->name('sekretaris.kalender');

        Route::get('/sekretaris/disposisi', function () {
            return view('suratMasuk&Dispo', ['title' => 'Sekretaris', 'role' => 'Sekretaris']);
        })->name('sekretaris.surat_masuk');

        Route::get(
            '/sekretaris/riwayat_verifikasi',
            [VerifikasiController::class, 'riwayat']
        )->name('sekretaris.riwayat');

        

        Route::get('/sekretaris/profil', function () {
            return view('profil', ['title' => 'Sekretaris', 'role' => 'Sekretaris']);
        })->name('sekretaris.profil');
    });


    // --------------------------------------------------------
    // 7. RUTE FRONTLINER (Role: J007)
    // --------------------------------------------------------
    Route::middleware('role:J007')->group(function () {
        Route::get(
            '/frontliner',
            [SuratController::class, 'info']
        )->name('frontliner.dashboard');;  

        Route::get('/frontliner/input_surat', function () {
            return view('frontliner.inputSurat', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.input_surat');

        Route::get('/frontliner/riwayat_input', function () {
            return view('frontliner.RiwayatInput', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.riwayat_input');

        Route::get('/frontliner/kalender_kantor', function () {
            return view('KalenderKantor', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.kalender');

        Route::get('/frontliner/profil', function () {
            return view('Profil', ['title' => 'Frontliner', 'role' => 'Frontliner']);
        })->name('frontliner.profil');

        Route::post('/surat/store', [SuratController::class, 'store'])
            ->name('surat.store');

        Route::get(
            '/frontliner/riwayat_input',
            [SuratController::class, 'index']
        );

        Route::get('/surat/{id}', [SuratController::class, 'show'])
            ->name('surat.show');

        Route::get('/surat/{id}/edit', [SuratController::class, 'edit'])
            ->name('surat.edit');

        Route::put('/surat/{id}', [SuratController::class, 'update'])
            ->name('surat.update');

        Route::delete('/surat/{id}', [SuratController::class, 'destroy'])
            ->name('surat.destroy');
    });
});
