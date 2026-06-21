// file: public/js/kalender-main.js

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        // Ambil data JSON dari blade
        var databaseEvents = JSON.parse(calendarEl.dataset.events || '[]');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'id',
            slotMinTime: '09:00:00',
            slotMaxTime: '20:00:00',
            allDaySlot: false,
            nowIndicator: true,
            height: '100%',
            dayHeaderFormat: { weekday: 'long' },
            events: databaseEvents,

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
                                ${props.lokasi || '-'}
                            </span>
                        </div>
                        <span class="badge-status mt-auto ${badgeClass}" style="font-size: 0.55rem; padding: 2px 0;">${badgeText}</span>
                    </div>
                `;
                return { html: html };
            },

            datesSet: function (info) {
                const judulKalender = document.getElementById('judul-kalender');
                if (judulKalender) {
                    judulKalender.innerText = info.view.title;
                }
            }
        });

        calendar.render();

        // 1. KONTROL FILTER DROPDOWN (Filter berdasarkan NIP pegawai dengan Tom Select)
        const filterStaff = document.getElementById('filter-staff');
        if (filterStaff) {
            // Inisialisasi Tom Select untuk searchable dropdown
            new TomSelect('#filter-staff', {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                onChange: function (selectedNip) {
                    let filteredEvents = selectedNip === 'all' 
                        ? databaseEvents 
                        : databaseEvents.filter(ev => {
                            return ev.extendedProps.daftar_staff && ev.extendedProps.daftar_staff.includes(selectedNip);
                        });

                    calendar.removeAllEvents();
                    calendar.addEventSource(filteredEvents);
                }
            });
        }

        // 2. KONTROL NAVIGATION PANAH (Diberi pelindung IF)
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        if (btnPrev) btnPrev.addEventListener('click', function () { calendar.prev(); });
        if (btnNext) btnNext.addEventListener('click', function () { calendar.next(); });

        // 3. KONTROL JENIS VIEW (HARI, MINGGU, BULAN)
        const btnHari = document.getElementById('btn-hari');
        const btnMinggu = document.getElementById('btn-minggu');
        const btnBulan = document.getElementById('btn-bulan');

        function resetBtnStyles() {
            [btnHari, btnMinggu, btnBulan].forEach(btn => {
                if (btn) {
                    btn.classList.remove('btn-white', 'fw-bold');
                    btn.classList.add('btn-light');
                    btn.style.borderWidth = '1px';
                }
            });
        }

        if (btnHari) {
            btnHari.addEventListener('click', function () {
                calendar.changeView('timeGridDay');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });
        }

        if (btnMinggu) {
            btnMinggu.addEventListener('click', function () {
                calendar.changeView('timeGridWeek');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });
        }

        if (btnBulan) {
            btnBulan.addEventListener('click', function () {
                calendar.changeView('dayGridMonth');
                resetBtnStyles();
                this.classList.remove('btn-light');
                this.classList.add('btn-white', 'fw-bold');
                this.style.borderWidth = '2px';
            });
        }
    }
});