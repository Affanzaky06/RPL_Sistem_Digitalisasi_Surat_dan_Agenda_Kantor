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
                    <div class="card shadow-sm mb-3 border-secondary-subtle rounded-3 notification-card" 
                         id="notif_{{ $notif->id }}" data-notif-id="notif_{{ $notif->id }}">
                        <div class="card-body position-relative">
                            <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="btn-close position-absolute top-0 end-0 m-3 btn-close-notif"
                                    style="font-size: 10px;" aria-label="Close"></button>
                            </form>

                            <h6 class="card-title fw-semibold text-primary">
                                <i class="bi bi-bell-fill me-1"></i> {{ $notif->data['title'] ?? 'Notifikasi' }}
                            </h6>
                            <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">
                                {{ $notif->data['body'] ?? '' }}
                            </p>
                            <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            <br>
                            <a href="{{ $notif->data['url'] ?? '#' }}" class="btn btn-dark btn-sm rounded-pill px-4 mt-2">Lihat Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border text-center text-muted rounded-3 py-4">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                        Belum ada notifikasi baru.
                    </div>
                @endforelse
            </div>



        </div>


        <x-card-agenda :ringkasanAgenda="$ringkasanAgenda" />
    </div>
</div>
