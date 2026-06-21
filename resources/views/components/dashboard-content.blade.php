{{-- @props([
    'jmlSurat' => 0, 
    'jmlNotif' => 0, 
    'jmlAgenda' => 0
    // Nanti Anda bisa menambahkan 'listAgenda' di sini saat datanya sudah ditarik dari database
]) --}}

<div class="container-fluid pt-2">
    <div class="row">

        <div class="col-lg-8">

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <i class="bi bi-envelope fs-2 me-2"></i>
                                <h2 class="display-6 mb-0 fw-normal">100</h2>
                            </div>
                            <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Surat Baru</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <i class="bi bi-bell fs-2 me-2"></i>
                                <h2 class="display-6 mb-0 fw-normal">100</h2>
                            </div>
                            <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Notifikasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm border-secondary-subtle rounded-3">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <i class="bi bi-calendar-check fs-2 me-2"></i>
                                <h2 class="display-6 mb-0 fw-normal">100</h2>
                            </div>
                            <p class="text-muted mb-0 mt-4" style="font-size: 20px;">Agenda Mendatang</p>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3 fw-semibold"><i class="bi bi-bell"></i> Notifikasi Terbaru</h5>

            <div id="notification-container">
                @forelse ($notifikasi as $notif)
                    {{-- ID Unik untuk notifikasi (berdasarkan id_disposisi dan statusnya supaya kalau status berubah notif muncul lagi) --}}
                    @php 
                        $notifId = 'notif_' . $notif->id_disposisi . '_' . Str::slug($notif->status); 
                    @endphp

                    <div class="card shadow-sm mb-3 border-secondary-subtle rounded-3 notification-card" 
                         id="{{ $notifId }}" data-notif-id="{{ $notifId }}">
                        <div class="card-body position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 btn-close-notif"
                                style="font-size: 10px;" aria-label="Close" data-target="{{ $notifId }}"></button>

                            @if($notif->status === 'Perwakilan')
                                <h6 class="card-title fw-semibold text-warning">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Perubahan Peran (Perwakilan)
                                </h6>
                                <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">
                                    Anda telah ditunjuk sebagai <strong>Perwakilan Kepala Kantor</strong> untuk agenda:
                                    <br><span class="text-dark fw-medium">{{ $notif->surat->perihal ?? 'Agenda Rapat' }}</span>
                                </p>
                            @else
                                <h6 class="card-title fw-semibold text-primary">
                                    <i class="bi bi-envelope-fill me-1"></i> Surat Masuk Baru
                                </h6>
                                <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">
                                    Ada surat / disposisi baru:
                                    <br><span class="text-dark fw-medium">{{ $notif->surat->perihal ?? 'Surat Baru' }}</span>
                                </p>
                            @endif
                            
                            <a href="{{ route(strtolower($role).'.surat_masuk') }}" class="btn btn-dark btn-sm rounded-pill px-4 mt-1">Lihat Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border text-center text-muted rounded-3 py-4">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                        Belum ada notifikasi baru.
                    </div>
                @endforelse
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // 1. Cek localStorage untuk notif yang sudah di-dismiss
                    const cards = document.querySelectorAll('.notification-card');
                    
                    // Observer untuk auto-hide saat masuk viewport
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const card = entry.target;
                                const notifId = card.dataset.notifId;
                                
                                // Tandai sudah dilihat di localStorage (biar ga muncul lagi pas refresh)
                                localStorage.setItem(notifId, 'seen');
                                
                                // Optional: Kita biarkan saja tampil saat ini, baru hilang pas di-refresh
                                // Tapi kalau mau auto hilang perlahan saat di-scroll:
                                // setTimeout(() => { card.style.display = 'none'; }, 3000); 
                                
                                // Stop observing after it's marked seen
                                observer.unobserve(card);
                            }
                        });
                    }, { threshold: 0.5 }); // Memicu jika 50% elemen terlihat

                    cards.forEach(card => {
                        const notifId = card.dataset.notifId;
                        
                        // Sembunyikan elemen jika sudah ada di localStorage
                        if (localStorage.getItem(notifId) === 'seen') {
                            card.style.display = 'none';
                        } else {
                            // Observasi elemen yang belum dilihat
                            observer.observe(card);
                        }
                    });

                    // 2. Event Listener untuk tombol ✕ (Close manual)
                    const closeBtns = document.querySelectorAll('.btn-close-notif');
                    closeBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const targetId = this.dataset.target;
                            const card = document.getElementById(targetId);
                            if(card) {
                                card.style.display = 'none';
                                localStorage.setItem(targetId, 'seen'); // Simpan ke localStorage
                            }
                        });
                    });
                });
            </script>

        </div>


        <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />
    </div>
</div>
