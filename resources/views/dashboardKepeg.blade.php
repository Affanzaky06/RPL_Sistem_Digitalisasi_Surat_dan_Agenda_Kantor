<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="border border-dark-subtle rounded-2 p-4 h-100 bg-white d-flex flex-column">

    <div class="container-fluid mt-1">
    <div class="row g-4 mb-5">
        
        <div class="col-md-4">
            <div class="card border border-dark rounded-3 shadow-sm h-100 text-center p-4">
                <h6 class="text-dark fw-medium mb-3">Total Pegawai Terdaftar</h6>
                <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                    <i class="bi bi-people text-dark" style="font-size: 3rem; line-height: 1;"></i>
                    <h1 class="display-4 fw-medium text-dark mb-0">{{ $totalPegawai }}</h1> </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Seluruh staf & pimpinan aktif</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border border-dark rounded-3 shadow-sm h-100 text-center p-4">
                <h6 class="text-dark fw-medium mb-3">Total Bidang & Jabatan</h6>
                <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                    <i class="bi bi-diagram-3 text-dark" style="font-size: 3rem; line-height: 1;"></i>
                    <h1 class="display-4 fw-medium text-dark mb-0">{{ $totalBidang }}</h1> </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Struktur organisasi saat ini</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border border-dark rounded-3 shadow-sm h-100 text-center p-4">
                <h6 class="text-dark fw-medium mb-3">Pembaruan Data (Bulan Ini)</h6>
                <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                    <i class="bi bi-person-lines-fill text-dark" style="font-size: 3rem; line-height: 1;"></i>
                    <h1 class="display-4 fw-medium text-dark mb-0">{{ $pembaruanBulanIni }}</h1> </div>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Data pegawai baru/diubah</p>
            </div>
        </div>

    </div>

       <div class="text-center my-3 flex-grow-1">
            <h5 class="fw-bold text-dark mb-2" id="tanggal-sekarang">Senin, 20 April 2026</h5>
            <h1 class="text-dark" style="font-size: 4rem; font-weight: 400;" id="jam-sekarang">23:55:50</h1>
        </div>

    <div class="mb-4">
        <h5 class="fw-bold text-dark text-center mb-4">Agenda dan Peserta Kantor</h5>
        </div>
</div>
    <div class="col-lg-3 ps-lg-4">
            <h4 class="fw-bold mb-3 fs-5">
                Ringkasan Agenda dan Peserta
            </h4>

            @forelse ($ringkasanAgenda as $index => $agenda)
                <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-0 text-dark">Meeting {{ $index + 1 }}:</h6>
                        <h6 class="fw-bold mb-2 text-dark">{{ $agenda->nomor_surat ?? 'AG-RANDOM' }}</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">
                            Peserta: {{ $agenda->perihal ?? 'Belum ada detail' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center p-3 text-muted" style="font-size: 0.85rem; border: 1px dashed #ccc; border-radius: 12px;">
                    <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                    Belum ada agenda terdaftar.
                </div>
            @endforelse
        </div>
</div>
</x-layout>
 <script>
        function updateJam() {
            const sekarang = new Date();
            
            // Format Tanggal (Contoh: Senin, 8 Juni 2026)
            const opsiTanggal = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const tanggalTeks = sekarang.toLocaleDateString('id-ID', opsiTanggal);
            
            // Format Jam (Contoh: 23:55:50)
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');
            
            // Masukkan ke HTML
            document.getElementById("tanggal-sekarang").innerHTML = tanggalTeks;
            document.getElementById("jam-sekarang").innerHTML = jam + ":" + menit + ":" + detik;
        }

        // Jalankan saat pertama kali dimuat
        updateJam();
        // Update setiap detik
        setInterval(updateJam, 1000);
    </script>