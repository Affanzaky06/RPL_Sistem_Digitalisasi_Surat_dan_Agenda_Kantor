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
