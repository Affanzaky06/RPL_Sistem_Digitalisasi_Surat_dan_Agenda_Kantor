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
    </style>

    <div class="container-fluid pt-2 px-3 position-relative" style="height: calc(100vh - 130px); overflow: hidden;">
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
                    <div id="calendar-agenda" class="flex-grow-1 p-2 overflow-auto"
                        data-events='@json($events)'></div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN PESERTA (Lebar 3) -->
        </div>

        <!-- Panggil File JS Khusus Agenda -->
        <script src="{{ asset('js/agenda.js') }}"></script>
</x-layout>
