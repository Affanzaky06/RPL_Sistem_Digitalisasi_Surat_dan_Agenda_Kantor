<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontliner\SuratController;
use App\Http\Controllers\Kabid\DisposisiKabidController;
use App\Http\Controllers\KalenderKantorController;
use App\Http\Controllers\KepalaKantor\DashboardKepalaController;
use App\Http\Controllers\KepalaKantor\DisposisiKepalaController;
use App\Http\Controllers\Kepegawaian\kepegawaianController;
use App\Http\Controllers\laporanPemantauanController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Sekretaris\DashboardSekretarisController;
use App\Http\Controllers\Sekretaris\VerifikasiController;
use App\Http\Controllers\Subkoor\DisposisiSubkoorController;
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
            [DashboardController::class, 'index']
        )->name('kepala.dashboard');

        Route::get(
            '/kepala/surat_masuk',
            [DisposisiKepalaController::class, 'index']
        )->name('kepala.surat_masuk');

        Route::post(
            '/kepala/konfirmasi-hadir/{id_surat}',
            [DisposisiKepalaController::class, 'konfirmasiHadir']
        )->name('kepala.konfirmasi_hadir');

        Route::post(
            '/kepala/tolak/{id_surat}',
            [DisposisiKepalaController::class, 'tolak']
        )->name('kepala.tolak');

        Route::get(
            '/kepala/agenda',
            [AgendaController::class, 'index']
        )->name('kepala.agenda');

        Route::get(
            '/kepala/Laporan_Pemantauan',
            [laporanPemantauanController::class, 'index']
        )->name('kepala.laporan');

        Route::get(
            '/kepala/kalender_kantor',
            [KalenderKantorController::class, 'index']
        )->name('kepala.kalender');

        Route::get(
            '/kepala/profil',
            [ProfilController::class, 'index']
        )->name('kepala.profil');

        Route::post(
            '/kepala/disposisi/{id}',
            [DisposisiKepalaController::class, 'disposisi']
        )->name('kepala.disposisi');

        Route::delete(
            '/kepala/disposisi/{id}',
            [DisposisiKepalaController::class, 'batalDisposisi']
        )->name('kepala.disposisi.batal');
    });

    // --------------------------------------------------------
    // 2. RUTE KABID (Role: J002)
    // --------------------------------------------------------

    Route::middleware('role:J002')->group(function () {
        Route::get(
            '/kabid',
            [DashboardController::class, 'index']
        )->name('kabid.dashboard');

        Route::get(
            '/kabid/surat_masuk',
            [DisposisiKabidController::class, 'index']
        )->name('kabid.surat_masuk');

        Route::get(
            '/kabid/agenda',
            [agendaController::class, 'index']
        )->name('kabid.agenda');

        Route::get(
            '/kabid/Laporan_Pemantauan',
            [laporanPemantauanController::class, 'index']
        )->name('kabid.laporan');

        Route::get(
            '/kabid/kalender_kantor',
            [KalenderKantorController::class, 'index']
        )->name('kabid.kalender');

        Route::get(
            '/kabid/profil',
            [ProfilController::class, 'index']
        )->name('kabid.profil');

        Route::post(
            '/kabid/disposisi/{id}',
            [DisposisiKabidController::class, 'disposisi']
        )->name('kabid.disposisi');

        Route::post(
            '/kabid/{id_surat}/hadir',
            [DisposisiKabidController::class, 'konfirmasiHadir']
        )->name('kabid.konfirmasi_hadir');

        Route::post(
            '/kabid/{id_surat}/tolak',
            [DisposisiKabidController::class, 'tolak']
        )->name('kabid.tolak');

        Route::delete(
            '/kabid/disposisi/{id}',
            [DisposisiKabidController::class, 'batalDisposisi']
        )->name('kabid.disposisi.batal');
    });

    // --------------------------------------------------------
    // 3. RUTE SUBKOOR (Role: J003)
    // --------------------------------------------------------

    Route::middleware('role:J003')->group(function () {

        Route::get(
            '/subkoor',
            [DashboardController::class, 'index']
        )->name('subkoor.dashboard');

        Route::get(
            '/subkoor/surat_masuk',
            [DisposisiSubkoorController::class, 'index']
        )->name('subkoor.surat_masuk');

        Route::post(
            '/subkoor/{id}/disposisi',
            [DisposisiSubkoorController::class, 'disposisi']
        )->name('subkoor.disposisi');

        Route::post(
            '/subkoor/{id_surat}/hadir',
            [DisposisiSubkoorController::class, 'konfirmasiHadir']
        )->name('subkoor.konfirmasi_hadir');

        Route::post(
            '/subkoor/{id_surat}/tolak',
            [DisposisiSubkoorController::class, 'tolak']
        )->name('subkoor.tolak');

        Route::delete(
            '/subkoor/disposisi/{id}',
            [DisposisiSubkoorController::class, 'batalDisposisi']
        )->name('subkoor.disposisi.batal');

        Route::get(
            '/subkoor/Laporan_Pemantauan',
            [laporanPemantauanController::class, 'index']
        )->name('subkoor.laporan');

        Route::get(
            '/subkoor/agenda',
            [agendaController::class, 'index']
        )->name('subkoor.agenda');

        Route::get(
            '/subkoor/kalender_kantor',
            [KalenderKantorController::class, 'index']
        )->name('subkoor.kalender');

        Route::get(
            '/subkoor/profil',
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

        Route::get(
            '/staff/agenda',
            [agendaController::class, 'index']
        )->name('staff.agenda');

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
    Route::get(
        '/kepegawaian',
        [kepegawaianController::class, 'index']
    )->name('kepegawaian.dashboard');

    Route::get('/kepegawaian/input_data', [kepegawaianController::class, 'inputPegawai'])
        ->name('kepegawaian.input_data');

    // ROUTE UNTUK MEMPROSES FORM
    Route::post('/kepegawaian/store_pegawai', [kepegawaianController::class, 'storePegawai'])
        ->name('kepegawaian.store');

    Route::get('/kepegawaian/list', [kepegawaianController::class, 'listPegawai'])
        ->name('kepegawaian.list');

    // TAMBAHKAN DUA ROUTE INI
    Route::put('/kepegawaian/update/{nip}', [kepegawaianController::class, 'updatePegawai'])
        ->name('kepegawaian.update');

    Route::delete('/kepegawaian/delete/{nip}', [kepegawaianController::class, 'destroyPegawai'])
        ->name('kepegawaian.delete');
    Route::get(
        '/kepegawaian/kalender_kantor',
        [KalenderKantorController::class, 'index']
    )->name('kepegawaian.kalender');

    Route::get(
        '/kepegawaian/profil',
        [ProfilController::class, 'index']
    )->name('kepegawian.profil');


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
        )
            ->name('sekretaris.verifikasi_surat');

        Route::put(
            '/sekretaris/verifikasi/{id}',
            [VerifikasiController::class, 'verifikasi']
        )->name('sekretaris.verifikasi');

        Route::put(
            '/sekretaris/tolak/{id}',
            [VerifikasiController::class, 'tolak']
        )->name('sekretaris.tolak');

        Route::get(
            '/sekretaris/agenda',
            [agendaController::class, 'index']
        )->name('sekretaris.agenda');

        Route::get(
            '/sekretaris/kalender_kantor',
            [KalenderKantorController::class, 'index']
        )->name('sekretaris.kalender');

        Route::get(
            '/sekretaris/disposisi',
            [DisposisiKepalaController::class, 'index']
        )->name('sekretaris.surat_masuk');

        Route::get(
            '/sekretaris/riwayat_verifikasi',
            [VerifikasiController::class, 'riwayat']
        )->name('sekretaris.riwayat');

        Route::get(
            '/sekretaris/profil',
            [ProfilController::class, 'index']
        )->name('sekretaris.profil');
    });


    // --------------------------------------------------------
    // 7. RUTE FRONTLINER (Role: J007)
    // --------------------------------------------------------
    Route::middleware('role:J007')->group(function () {
        Route::get(
            '/frontliner',
            [SuratController::class, 'info']
        )->name('frontliner.dashboard');;

        Route::get(
            '/frontliner/input_surat',
            [SuratController::class, 'create']
        )->name('frontliner.input_surat');

        Route::get(
            '/frontliner/riwayat_input',
            [SuratController::class, 'index']
        );

        Route::get(
            '/frontliner/kalender_kantor',
            [KalenderKantorController::class, 'index']
        )->name('frontliner.kalender');

        Route::get(
            '/frontliner/profil',
            [ProfilController::class, 'index']
        )->name('frontliner.profil');

        Route::post('/surat/store', [SuratController::class, 'store'])
            ->name('surat.store');


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
