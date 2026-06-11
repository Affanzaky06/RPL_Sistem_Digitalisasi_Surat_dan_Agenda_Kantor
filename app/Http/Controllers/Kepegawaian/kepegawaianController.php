<?php

namespace App\Http\Controllers\Kepegawaian;

use App\Http\Controllers\Controller;
use App\Models\Pegawai; // Wajib dipanggil untuk metrik card
use App\Models\Surat;   // Wajib dipanggil untuk agenda bawah
use App\Models\Bidang;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class KepegawaianController extends Controller
{
    public function index()
    {
        // 1. Tangkap data user yang sedang login
        $user = Auth::user();
        $role = 'Kepegawaian';

        // =================================================================
        // 2. DATA METRIK UNTUK 3 CARD DI ATAS (Data HR/SDM)
        // =================================================================
        
        // A. Total seluruh pegawai yang terdaftar
        $totalPegawai = Pegawai::count();

        // B. Total bidang (Mengambil jumlah id_bidang unik yang tidak null)
        // Jika Anda punya Model Bidang terpisah, bisa diganti jadi Bidang::count();
        $totalBidang = Pegawai::whereNotNull('id_bidang')->distinct('id_bidang')->count();

        // C. Jumlah pembaruan data pegawai di bulan dan tahun ini
        $pembaruanBulanIni = Pegawai::whereMonth('updated_at', Carbon::now()->month)
                                    ->whereYear('updated_at', Carbon::now()->year)
                                    ->count();

        // =================================================================
        // 3. DATA AGENDA UNTUK BAGIAN BAWAH
        // =================================================================
        
        // Menampilkan 3 agenda kantor terdekat
        $ringkasanAgenda = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->take(3)
            ->get();

        // =================================================================
        // 4. LEMPAR SEMUA DATA KE VIEW BLADE
        // =================================================================
        return view('dashboardKepeg', [
            'title' => 'Dashboard Kepegawaian',
            'role' => $role,
            'user' => $user,
            
            // Variabel untuk Card
            'totalPegawai' => $totalPegawai,
            'totalBidang' => $totalBidang,
            'pembaruanBulanIni' => $pembaruanBulanIni,
            
            // Variabel untuk List Bawah
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }

    public function inputPegawai()
    {
        $user = Auth::user();
        $role = 'Kepegawaian';

        // Ambil data agenda untuk sidebar kanan (sama seperti di dashboard)
        $ringkasanAgenda = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->take(3)
            ->get();

        return view('inputPegawai', [ // Sesuaikan dengan nama file blade Anda
            'title' => 'Input Data Pegawai',
            'role' => $role,
            'ringkasanAgenda' => $ringkasanAgenda
        ]);
    }

    // MEMPROSES PENYIMPANAN DATA KE DATABASE
    public function storePegawai(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'nip' => 'required|unique:pegawai,nip|max:50',
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'bidang' => 'required',
            'jabatan' => 'required',
            'berkas_pegawai' => 'required|file|mimes:pdf,jpg,jpeg|max:2048', // Maksimal 2MB
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar di sistem.',
            'berkas_pegawai.mimes' => 'Format file harus PDF, JPG, atau JPEG.'
        ]);

        // 2. Proses Upload File
        $filePath = null;
        if ($request->hasFile('berkas_pegawai')) {
            $file = $request->file('berkas_pegawai');
            // Format nama file: NIP_NamaFileAsli
            $filename = $request->nip . '_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan ke folder storage/app/public/berkas_pegawai
            $filePath = $file->storeAs('berkas_pegawai', $filename, 'public');
        }

        // 3. Simpan ke database
        Pegawai::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_bidang' => $request->bidang,
            'id_jabatan' => $request->jabatan,
            'foto_profil' => $filePath, // Kita simpan path file ke kolom ini sesuai migration
            'password' => Hash::make('Pegawai123'), // Password default
            // nip_atasan dibiarkan null dulu untuk saat ini
        ]);

        // 4. Kembali ke halaman form dengan pesan sukses
        return redirect()->back()->with('success', 'Data pegawai berhasil ditambahkan!');
    }

    // MENAMPILKAN HALAMAN LIST PEGAWAI (Dengan Search & Sort)
    public function listPegawai(Request $request)
    {
        $user = Auth::user();
        $role = 'Kepegawaian';

        $ringkasanAgenda = Surat::with('disposisi')
            ->whereNotNull('tanggal_kegiatan')
            ->whereDate('tanggal_kegiatan', '>=', Carbon::today())
            ->orderBy('tanggal_kegiatan', 'asc')
            ->take(3)
            ->get();

        $search = $request->input('search');
        $sort = $request->input('sort', 'default'); // Ambil nilai sort

        $queryPegawai = Pegawai::with(['jabatan', 'bidang']);

        // Fitur Searching
        if ($search) {
            $queryPegawai->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        // Fitur Sorting
        if ($sort === 'nama_asc') {
            $queryPegawai->orderBy('nama', 'asc');
        } elseif ($sort === 'nama_desc') {
            $queryPegawai->orderBy('nama', 'desc');
        } else {
            $queryPegawai->latest(); // Default: yang terbaru ditambahkan
        }

        $daftarPegawai = $queryPegawai->paginate(5);

        $semuaBidang = Bidang::all();
        $semuaJabatan = Jabatan::all();

        return view('listPegawai', [
            'title' => 'List Pegawai',
            'role' => $role,
            'ringkasanAgenda' => $ringkasanAgenda,
            'daftarPegawai' => $daftarPegawai,
            'search' => $search,
            'sort' => $sort, // Kirim data sort ke view
            'semuaBidang' => $semuaBidang,
            'semuaJabatan' => $semuaJabatan
        ]);
    }

    // MEMPROSES UPDATE DATA PEGAWAI DARI MODAL
public function updatePegawai(Request $request, $nip)
    {
        // 1. Ambil data pegawai yang lama dari database
        $pegawai = Pegawai::where('nip', $nip)->firstOrFail();

        // 2. Validasi input form
        $request->validate([
            'nip' => 'required|string|max:50|unique:pegawai,nip,' . $nip . ',nip', 
            'nama' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'bidang' => 'required',
            'jabatan' => 'required',
            'berkas_pegawai' => 'nullable|file|mimes:pdf,jpg,jpeg|max:2048',
        ]);

        // 3. KUNCI RAHASIA: Amankan nama file lama sebagai nilai default!
        $filePath = $pegawai->foto_profil;

        // 4. Jika user MENGUNGGAH file baru, barulah kita timpa variabel $filePath di atas
        if ($request->hasFile('berkas_pegawai')) {
            $file = $request->file('berkas_pegawai');
            $filename = $request->nip . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('berkas_pegawai', $filename, 'public');
            
            // (Opsional) Jika ingin otomatis menghapus file lama di server agar storage tidak penuh:
            // if ($pegawai->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($pegawai->foto_profil)) {
            //     \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto_profil);
            // }
        }

        // 5. Update database menggunakan Query Builder
        Pegawai::where('nip', $nip)->update([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'tanggal_lahir' => $request->tanggal_lahir,
            'id_bidang' => $request->bidang,
            'id_jabatan' => $request->jabatan,
            'foto_profil' => $filePath // Sekarang ini pasti aman, berisi file lama ATAU file baru
        ]);

        return redirect()->back()->with('success', 'Data pegawai berhasil diperbarui!');
    }
    // MEMPROSES HAPUS DATA PEGAWAI
    public function destroyPegawai($nip)
    {
        $pegawai = Pegawai::where('nip', $nip)->firstOrFail();
        $pegawai->delete();

        return redirect()->back()->with('success', 'Data pegawai berhasil dihapus!');
    }
}