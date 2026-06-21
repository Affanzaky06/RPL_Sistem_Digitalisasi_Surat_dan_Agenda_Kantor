<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>

    <!-- Panggil FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <style>
        /* CSS Kustomisasi Kalender Sesuai Mockup */
        .fc-event,
        .fc-v-event,
        .fc-timegrid-event {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .fc-event-main {
            padding: 0 !important;
            border: none !important;
        }

        .fc-timegrid-slot {
            height: 50px !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #495057;
        }

        .fc-col-header-cell {
            padding: 8px 0 !important;
            background-color: #fff;
            border-bottom: 1px solid #212529 !important;
        }

        .fc-timegrid-axis {
            border-bottom: 1px solid #212529 !important;
        }

        .fc-timegrid-now-indicator-line {
            border-color: #dc3545;
            border-width: 2px;
        }

        .fc-col-header-cell-cushion {
            color: #212529 !important;
            text-decoration: none !important;
            font-weight: lighter !important;
            font-size: 16px !important;
        }

        .fc-timegrid-slot-label-cushion {
            font-size: 14px !important;
            font-weight: normal !important;
            color: #212529 !important;
        }

        .bg-terlaksana {
            background-color: #fff3cd !important;
            border: 1px solid #ffe69c !important;
        }

        .bg-berlangsung {
            background-color: #d1e7dd !important;
            border: 1px solid #a3cfbb !important;
        }

        .bg-mendatang {
            background-color: #cfe2ff !important;
            border: 1px solid #9ec5fe !important;
        }

        .badge-status {
            font-size: 0.6rem;
            padding: 3px 0;
            display: block;
            text-align: center;
            border-radius: 4px;
            font-weight: 600;
        }

        .badge-terlaksana {
            background-color: #ffc107;
            color: #fff;
        }

        .badge-berlangsung {
            background-color: #198754;
            color: #fff;
        }

        .badge-mendatang {
            background-color: #0d6efd;
            color: #fff;
        }

        .fc-header-toolbar {
            display: none !important;
        }

        /* Popup Card Style */
        #event-popup-card {
            display: none;
            position: absolute;
            z-index: 1050;
            width: 320px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            padding: 16px;
        }

        #event-popup-card .popup-close {
            position: absolute;
            top: 8px;
            right: 12px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6c757d;
        }

        #event-popup-card .popup-close:hover {
            color: #212529;
        }
    </style>

    <div class="container-fluid pt-2 px-3 position-relative" style="height: calc(100vh - 130px); overflow: hidden;">

        @if (session('success'))
            <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                <div class="toast show border-0 shadow">
                    <div class="toast-body">
                        <span class="text-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                        </span>
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="position-fixed top-0 end-0 p-3" style="z-index:9999;">
                <div class="toast show border-0 shadow">
                    <div class="toast-body">
                        <span class="text-danger">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                        </span>
                        {{ $errors->first() }}
                    </div>
                </div>
            </div>
        @endif
        <script>
            setTimeout(() => {
                document.querySelectorAll('.toast').forEach(el => el.remove());
            }, 6000);
        </script>

        <div class="row h-100">

            <!-- KOLOM KIRI: KALENDER (Lebar 9) -->
            <div class=" h-100">
                <div class="border border-dark rounded-3 d-flex flex-column h-100 bg-white shadow-sm overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-dark">

                        <div class="d-flex align-items-center gap-3">
                            <button id="btn-prev-agenda" class="btn btn-sm btn-light border-0 fs-5"><i
                                    class="bi bi-arrow-left"></i></button>
                            <button id="btn-next-agenda" class="btn btn-sm btn-light border-0 fs-5"><i
                                    class="bi bi-arrow-right"></i></button>
                            <h5 id="judul-agenda" class="mb-0 fw-medium text-dark ms-2" style="font-size: 1.1rem;">
                                Memuat...</h5>
                        </div>

                        <!-- Grup Tombol View -->
                        <div class="btn-group border border-dark rounded-2" role="group">
                            <button id="btn-hari-agenda" type="button"
                                class="btn btn-light px-3 py-1 border-end border-dark text-dark"
                                style="font-size: 0.9rem;">Hari</button>
                            <button id="btn-minggu-agenda" type="button"
                                class="btn btn-white px-3 py-1 border-end border-dark fw-bold text-dark"
                                style="border-width: 2px !important; font-size: 0.9rem;">Minggu</button>
                            <button id="btn-bulan-agenda" type="button" class="btn btn-light px-3 py-1 text-dark"
                                style="font-size: 0.9rem;">Bulan</button>
                        </div>

                    </div>

                    <!-- Target Mesin FullCalendar -->
                    @php
                        $disposisiCandidates = $pegawaiDisposisi
                            ->map(function ($pegawai) {
                                return [
                                    'nip' => $pegawai->nip,
                                    'nama' => $pegawai->nama,
                                    'jabatan' => $pegawai->jabatan->nama_jabatan ?? $pegawai->id_jabatan,
                                    'bidang' => $pegawai->bidang->nama_bidang ?? null,
                                ];
                            })
                            ->values();
                    @endphp
                    <div id="calendar-agenda" class="flex-grow-1 p-2 overflow-auto"
                        data-events='@json($events)' data-role="{{ $role }}"
                        data-disposisi-candidates='@json($disposisiCandidates)'>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN PESERTA (Lebar 3) -->
        </div>

        <!-- POPUP CARD (muncul saat klik event di kalender) -->
        <div id="event-popup-card">
            <button class="popup-close" id="popup-close-btn">&times;</button>
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-calendar-event me-2 text-muted"></i>
                <h6 class="fw-bold mb-0" id="popup-title"></h6>
            </div>
            <div class="text-muted mb-1" style="font-size: 0.8rem;">
                <i class="bi bi-clock me-1"></i> <span id="popup-waktu"></span>
            </div>
            <div class="text-muted mb-2" style="font-size: 0.8rem;">
                <i class="bi bi-calendar3 me-1"></i> <span id="popup-tanggal"></span>
            </div>
            <div class="text-muted mb-2" style="font-size: 0.8rem;">
                <i class="bi bi-geo-alt me-1"></i> <span id="popup-lokasi"></span>
            </div>
            <div class="mb-3" id="popup-agenda-list">
                <strong>Agenda</strong>
                <div id="popup-perihal" class="text-muted" style="font-size: 0.85rem;"></div>
            </div>
            <button type="button" class="btn btn-danger btn-sm" id="popup-batal-hadir-btn">
                <i class="bi bi-x-lg me-1"></i> Batal Hadir
            </button>
        </div>

    </div>

    <!-- MODAL TIDAK HADIR (untuk Kabid ke bawah, dengan alasan) -->
    <div class="modal fade" id="tidakHadirModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form id="form-tidak-hadir" method="POST" action="">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 mt-2 px-4">
                        <h5 class="modal-title fw-bold fs-4">Tidak Hadir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="border rounded-3 p-3 mb-4 bg-white shadow-sm d-flex position-relative">
                            <div class="col-6 pe-3" style="border-right: 2px dashed #dee2e6;">
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i
                                            class="bi bi-send me-2"></i>Pengirim</small>
                                    <span class="fw-bold text-dark" id="modal-pengirim"></span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-hash me-2"></i>Nomor
                                        Surat</small>
                                    <span class="fw-bold text-dark" id="modal-nomor-surat"></span>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1"><i
                                            class="bi bi-file-earmark-text me-2"></i>Perihal Surat</small>
                                    <span class="fw-bold text-dark" id="modal-perihal"></span>
                                </div>
                            </div>

                            <div class="col-6 ps-4 position-relative">
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i class="bi bi-calendar me-2"></i>Tanggal
                                        Surat</small>
                                    <span class="fw-bold text-dark" id="modal-tanggal-surat"></span>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1"><i
                                            class="bi bi-clock me-2"></i>Waktu</small>
                                    <span class="fw-bold text-dark" id="modal-waktu"></span>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1"><i
                                            class="bi bi-info-circle me-2"></i>Prioritas</small>
                                    <span id="modal-prioritas"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Alasan Tidak Hadir (hanya untuk Kabid ke bawah) -->
                        <div class="border rounded-3 p-3 bg-white shadow-sm" id="alasan-container">
                            <label class="fw-bold mb-2 d-flex align-items-center text-dark">
                                <i class="bi bi-pencil-square me-2 fs-5"></i> Alasan Tidak Hadir
                            </label>
                            <textarea name="alasan_tidak_hadir" rows="4" class="form-control border-secondary-subtle"
                                placeholder="Tulis Alasan Disini..." required></textarea>
                        </div>

                        <div class="border rounded-3 p-3 bg-white shadow-sm mt-3 d-none"
                            id="disposisi-pengganti-container">
                            <label class="fw-bold mb-2 d-flex align-items-center text-dark">
                                <i class="bi bi-person-plus me-2 fs-5"></i> Disposisikan ke Pengganti
                            </label>
                            <select name="nip_pengganti" class="form-select border-secondary-subtle mb-3"
                                id="select-pengganti">
                                <option value="">Pilih pegawai</option>
                            </select>
                            <textarea name="catatan_pengganti" rows="3" class="form-control border-secondary-subtle"
                                placeholder="Catatan disposisi (opsional)"></textarea>
                            <small class="text-muted d-block mt-2" id="disposisi-pengganti-help"></small>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 justify-content-end">
                        <button type="submit" class="btn btn-success px-4 fw-bold"
                            style="background-color: #198754;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Panggil File JS Khusus Agenda -->
    <script src="{{ asset('js/agenda.js') }}"></script>
</x-layout>
