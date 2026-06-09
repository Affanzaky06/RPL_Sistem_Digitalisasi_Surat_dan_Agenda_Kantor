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
                    <select class="form-select border-dark text-dark fw-medium ps-3 pe-5 py-1 rounded-2"
                        style="appearance: none; background-image: none !important; font-size: 0.9rem;">
                        <option>Kabid-Pembangunan</option>
                        <option>Kabid-Keuangan</option>
                        <option>Kabid-Operasional</option>
                    </select>
                    <div class="position-absolute top-50 translate-middle-y end-0 pe-2 d-flex gap-1"
                        style="pointer-events: none;">
                        <i class="bi bi-search text-dark" style="font-size: 0.85rem;"></i>
                        <i class="bi bi-chevron-down text-dark" style="font-size: 0.85rem;"></i>
                    </div>
                </div>

            </div>

            <div id="calendar" class="flex-grow-1 p-2 overflow-auto"></div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'id',
                slotMinTime: '09:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                nowIndicator: true,
                height: '100%',
                dayHeaderFormat: { weekday: 'long' },

                events: [
                    {
                        title: 'Rapat Koordinasi MBG',
                        start: '2026-12-01T10:30:00',
                        end: '2026-12-01T14:30:00',
                        extendedProps: {
                            lokasi: 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                            status: 'terlaksana'
                        }
                    },
                    {
                        title: 'Rapat Koordinasi MBG',
                        start: '2026-12-02T09:30:00',
                        end: '2026-12-02T13:00:00',
                        extendedProps: {
                            lokasi: 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                            status: 'berlangsung'
                        }
                    },
                    {
                        title: 'Rapat Koordinasi Kominfo',
                        start: '2026-12-03T14:30:00',
                        end: '2026-12-03T18:30:00',
                        extendedProps: {
                            lokasi: 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                            status: 'mendatang'
                        }
                    },
                    {
                        title: 'Rapat Koordinasi Kemendagri',
                        start: '2026-12-04T10:30:00',
                        end: '2026-12-04T17:00:00',
                        extendedProps: {
                            lokasi: 'Jl. Jend Sudirman, Jakarta. Kantor MBG, Ruang Rapat Kemendagri, Lt. 5',
                            status: 'mendatang'
                        }
                    }
                ],

                eventContent: function (arg) {
                    let event = arg.event;
                    let props = event.extendedProps;

                    let start = event.start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    let end = event.end ? event.end.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';

                    let bgClass = '';
                    let badgeClass = '';
                    let badgeText = '';

                    if (props.status === 'terlaksana') {
                        bgClass = 'bg-terlaksana'; badgeClass = 'badge-terlaksana'; badgeText = 'Terlaksana';
                    } else if (props.status === 'berlangsung') {
                        bgClass = 'bg-berlangsung'; badgeClass = 'badge-berlangsung'; badgeText = 'Sedang Berlangsung';
                    } else {
                        bgClass = 'bg-mendatang'; badgeClass = 'badge-mendatang'; badgeText = 'Akan Datang';
                    }

                    // TAMPILAN CUSTOM KOTAK ACARA (W-100 H-100 MEMASTIKAN PENUH)
                    let html = `
                        <div class="${bgClass} w-100 h-100 d-flex flex-column" style="border-radius: 4px; padding: 5px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden;">
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.55rem; margin-bottom: 2px;">
                                <span>${start}-${end}</span>
                                <i class="bi bi-info-circle"></i>
                            </div>
                            
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.7rem; line-height: 1.1;">${event.title}</h6>
                            
                            <div class="text-muted d-flex mt-1 mb-1" style="font-size: 0.55rem; line-height: 1.1;">
                                <i class="bi bi-geo-alt me-1" style="margin-top: 1px;"></i>
                                <span style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    ${props.lokasi}
                                </span>
                            </div>
                            
                            <span class="badge-status mt-auto ${badgeClass}" style="font-size: 0.55rem; padding: 2px 0;">${badgeText}</span>
                        </div>
                    `;
                    return { html: html };
                },

                datesSet: function (info) {
                    document.getElementById('judul-kalender').innerText = info.view.title;
                }
            });

            calendar.render();

            // Kontrol Tombol Custom
            document.getElementById('btn-prev').addEventListener('click', function () { calendar.prev(); });
            document.getElementById('btn-next').addEventListener('click', function () { calendar.next(); });

            const btnHari = document.getElementById('btn-hari');
            const btnMinggu = document.getElementById('btn-minggu');
            const btnBulan = document.getElementById('btn-bulan');

            function resetBtnStyles() {
                [btnHari, btnMinggu, btnBulan].forEach(btn => {
                    btn.classList.remove('btn-white', 'fw-bold');
                    btn.classList.add('btn-light');
                    btn.style.borderWidth = '1px';
                });
            }

            btnHari.addEventListener('click', function () {
                calendar.changeView('timeGridDay');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });

            btnMinggu.addEventListener('click', function () {
                calendar.changeView('timeGridWeek');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });

            btnBulan.addEventListener('click', function () {
                calendar.changeView('dayGridMonth');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });

            calendar.gotoDate('2026-12-01');
        });
    </script>
</x-layout>