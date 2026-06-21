<div class="col-lg-3 ps-lg-4">
    <h4 class="fw-bold mb-3 fs-5">
        Ringkasan Agenda dan Peserta
    </h4>

    @forelse ($ringkasanAgenda as $index => $agenda)
        <div class="card border-0 mb-3 shadow-sm" style="background-color: #f4f5f7; border-radius: 12px;">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-1 text-primary">
                    <i class="bi bi-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($agenda->waktu_mulai)->format('H:i') }} WIB
                    <span class="text-muted small fw-normal">({{ \Carbon\Carbon::parse($agenda->tanggal_kegiatan)->format('d M') }})</span>
                </h6>
                <h6 class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">{{ $agenda->nama_kegiatan }}</h6>
                
                <p class="text-dark mb-2" style="font-size: 0.8rem; line-height: 1.4;">
                    <strong>Peserta:</strong> <br>
                    <span class="text-secondary">
                        {{ $agenda->peserta->filter(function($p) {
                            return $p->status_kehadiran === 'Hadir';
                        })->map(function($p) {
                            return $p->pegawai->nama ?? $p->nip;
                        })->implode(', ') ?: '-' }}
                    </span>
                </p>

                <small class="text-muted d-block" style="font-size: 0.72rem; border-top: 1px dashed #ccc; pt-1">
                    Dasar: {{ $agenda->surat->nomor_surat ?? '-' }}
                </small>
            </div>
        </div>
    @empty
        <div class="text-center p-3 text-muted" style="font-size: 0.85rem; border: 1px dashed #ccc; border-radius: 12px;">
            <i class="bi bi-calendar-x fs-4 d-block mb-1"></i>
            Belum ada agenda terdaftar.
        </div>
    @endforelse
</div>