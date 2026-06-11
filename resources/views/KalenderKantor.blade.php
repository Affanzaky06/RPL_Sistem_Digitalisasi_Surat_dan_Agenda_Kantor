<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <style>
        /* MENGHANCURKAN BORDER DAN BACKGROUND BIRU BAWAAN KALENDER */
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

        /* Kustomisasi Kalender */
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
            font-size: 20px !important;
        }

        .fc-timegrid-slot-label-cushion {
            font-size: 20px !important;
            font-weight: normal !important;
            color: #212529 !important;
        }

        .fc-daygrid-day-number {
            color: #212529 !important;
            text-decoration: none !important;
            font-size: 0.9rem !important; /* Ukuran bisa dikecilkan lagi jika perlu */
            font-weight: 500 !important;
            padding: 4px 8px !important;
        }
        .fc-daygrid-day-number:hover {
            color: #000 !important;
        }

        /* Warna Status Custom */
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

    <div class="container-fluid pt-2 px-3 pb-3" style="height: calc(100vh - 130px); overflow: hidden;">

        <div class="border border-dark rounded-3 d-flex flex-column h-100 bg-white shadow-sm overflow-hidden">

            <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-dark">

                <div class="d-flex align-items-center gap-3">
                    <button id="btn-prev" class="btn btn-sm btn-light border-0 fs-5"><i
                            class="bi bi-arrow-left"></i></button>
                    <button id="btn-next" class="btn btn-sm btn-light border-0 fs-5"><i
                            class="bi bi-arrow-right"></i></button>
                    <h5 id="judul-kalender" class="mb-0 fw-medium text-dark ms-2" style="font-size: 1.1rem;">Memuat...
                    </h5>
                </div>

                <div class="btn-group border border-dark rounded-2" role="group">
                    <button id="btn-hari" type="button" class="btn btn-light px-3 py-1 border-end border-dark text-dark"
                        style="font-size: 0.9rem;">Hari</button>
                    <button id="btn-minggu" type="button"
                        class="btn btn-white px-3 py-1 border-end border-dark fw-bold text-dark"
                        style="border-width: 2px !important; font-size: 0.9rem;">Minggu</button>
                    <button id="btn-bulan" type="button" class="btn btn-light px-3 py-1 text-dark"
                        style="font-size: 0.9rem;">Bulan</button>
                </div>

               <div class="position-relative" style="width: 220px;">
                    <select id="filter-staff" class="form-select border-dark text-dark fw-medium ps-3 pe-5 py-1 rounded-2" style="appearance: none; font-size: 0.9rem;">
                        <option value="all">Semua Staff / Agenda</option>
                        
                        @foreach($daftarStaff as $nama)
                            <option value="{{ $nama }}">{{ $nama }}</option>
                        @endforeach
                        
                    </select>
                    <div class="position-absolute top-50 translate-middle-y end-0 pe-2 d-flex gap-1" style="pointer-events: none;">
                        <i class="bi bi-search text-dark mx-4" style="font-size: 0.85rem; "></i>
                        {{-- <i class="bi bi-chevron-down text-dark" style="font-size: 0.85rem;"></i> --}}
                    </div>
                </div>

            </div>

            <div id="calendar" class="flex-grow-1 p-2 overflow-auto"></div>

        </div>
    </div>
    <script src="{{ asset('js/kalender_kantor.js') }}"></script>
</x-layout>