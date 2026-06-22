<x-layout :role="$role">

    <x-slot:title>
        {{ $title }}
    </x-slot:title>


    <div class="row g-4 mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-envelope fs-1"></i>

                    <h1>{{ $totalSurat }}</h1>

                    <small>
                        Total Surat Masuk
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-bell fs-1"></i>

                    <h1>{{ count($notifikasi) }}</h1>

                    <small>
                        Notifikasi
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <i class="bi bi-file-earmark-text fs-1"></i>

                    <h1>{{ $menungguVerifikasi }}</h1>

                    <small>
                        Menunggu Verifikasi
                    </small>

                </div>

            </div>

        </div>

    </div>

    <h4 class="mb-3">

        <i class="bi bi-bell"></i>

        Notifikasi Terbaru

    </h4>

    @forelse($notifikasi as $notif)
        <div class="card mb-3 shadow-sm position-relative">
            <form action="{{ route('notifications.read', $notif->id) }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="btn-close position-absolute top-0 end-0 m-2" style="z-index: 10;"
                    aria-label="Close"></button>
            </form>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-1 text-primary">
                            <i class="bi bi-bell-fill me-1"></i> {{ $notif->data['title'] ?? 'Notifikasi' }}
                        </h6>
                        <p class="mb-1">
                            {{ $notif->data['body'] ?? '' }}
                        </p>
                        <small class="text-muted">
                            {{ $notif->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">
            Belum ada notifikasi terbaru.
        </div>
    @endforelse
</x-layout>
