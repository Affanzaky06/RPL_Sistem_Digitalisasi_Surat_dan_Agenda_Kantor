@props([
    'jmlSurat' => 0, 
    'jmltolak' => 0, 
    'TungguVeriv' => 0,
    'ringkasanAgenda' =>0
    // Nanti Anda bisa menambahkan 'listAgenda' di sini saat datanya sudah ditarik dari database
])
    
    
    <div class="border border-dark-subtle rounded-2 p-4 h-100 bg-white d-flex flex-column">

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100 text-center border-dark-subtle py-2 rounded-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <p class="mb-2 text-dark fs-5">Total Surat Masuk (Hari ini)</p>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <i class="bi bi-envelope fs-1 me-3 text-dark"></i>
                            <h1 class="display-4 mb-0 text-dark">{{ $jmlSurat }}</h1>
                        </div>
                        <p class="mb-0 text-dark fs-5">Telah Diinput</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 text-center border-dark-subtle py-2 rounded-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-center align-items-center mb-2 mt-4">
                            <i class="bi bi-file-earmark-x fs-1 me-3 text-dark"></i>
                            <h1 class="display-4 mb-0 text-dark"> {{ $jmltolak }}</h1>
                        </div>
                        <p class="mb-0 mt-2 text-dark fs-5">Surat Ditolak Sekretaris</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 text-center border-dark-subtle py-2 rounded-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <p class="mb-2 text-dark fs-5">Menunggu Verivikasi</p>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <i class="bi bi-file-earmark-text fs-1 me-3 text-dark"></i>
                            <h1 class="display-4 mb-0 text-dark">{{ $TungguVeriv }}</h1>
                        </div>
                        <p class="mb-0 text-dark fs-5">Selalu Pantau surat Urgent</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center my-3 flex-grow-1">
            <h5 class="fw-bold text-dark mb-2" id="tanggal-sekarang">Senin, 20 April 2026</h5>
            <h1 class="text-dark" style="font-size: 4rem; font-weight: 400;" id="jam-sekarang">23:55:50</h1>
        </div>

        <div class="mt-2">
            <h5 class="fw-bold text-dark text-center mb-4">Agenda dan Peserta Kantor</h5>
            <div class="row g-3 justify-content-center">
                @forelse ($ringkasanAgenda as $index => $agenda)
                    <div class="col-md-4">
                        <div class="card border-0 h-100 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">Meeting {{ $index + 1 }}:</h6>
                                <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">{{ $agenda->surat->nomor_surat ?? '-' }}</h6>
                                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                    <strong>Peserta:</strong> 
                                    {{ $agenda->peserta->filter(fn($p) => $p->status_kehadiran === 'Hadir')->map(fn($p) => $p->pegawai->nama ?? $p->nip)->implode(', ') ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center p-3 text-muted" style="font-size: 0.85rem; border: 1px dashed #ccc; border-radius: 12px;">
                            <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
                            Belum ada agenda terdaftar.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

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