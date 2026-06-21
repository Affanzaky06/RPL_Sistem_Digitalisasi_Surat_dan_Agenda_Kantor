// file: public/js/agenda.js

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar-agenda');

    if (calendarEl) {
        let databaseEvents = JSON.parse(calendarEl.dataset.events || '[]');
        let userRole = calendarEl.dataset.role || '';
        let disposisiCandidates = JSON.parse(calendarEl.dataset.disposisiCandidates || '[]');

        // Referensi ke popup card
        const popupCard = document.getElementById('event-popup-card');
        const popupCloseBtn = document.getElementById('popup-close-btn');
        const popupTitle = document.getElementById('popup-title');
        const popupWaktu = document.getElementById('popup-waktu');
        const popupTanggal = document.getElementById('popup-tanggal');
        const popupLokasi = document.getElementById('popup-lokasi');
        const popupPerihal = document.getElementById('popup-perihal');
        const popupBatalBtn = document.getElementById('popup-batal-hadir-btn');

        // Data event yang sedang aktif di popup
        let activeEventProps = null;

        function hasAvailablePendamping() {
            return userRole === 'Kepala'
                && Array.isArray(activeEventProps?.pendamping_hadir)
                && activeEventProps.pendamping_hadir.length > 0;
        }

        function needsReplacementDisposisi() {
            if (!activeEventProps) return false;
            if (hasAvailablePendamping()) return false;

            return Number(activeEventProps.jumlah_peserta_hadir || 0) <= 1
                && (userRole === 'Kepala' || userRole === 'Kabid' || userRole === 'Subkoor');
        }

        function fillAttendanceModal(formAction) {
            let modalForm = document.getElementById('form-tidak-hadir');
            modalForm.action = formAction;

            document.getElementById('modal-pengirim').textContent = activeEventProps.pengirim || '-';
            document.getElementById('modal-nomor-surat').textContent = activeEventProps.nomor_surat || '-';
            document.getElementById('modal-perihal').textContent = activeEventProps.perihal || '-';
            document.getElementById('modal-tanggal-surat').textContent = activeEventProps.tanggal_surat || '-';
            document.getElementById('modal-waktu').textContent = activeEventProps.waktu || '-';

            let prioritasEl = document.getElementById('modal-prioritas');
            let prioritas = activeEventProps.prioritas || 'Rendah';
            if (prioritas === 'Tinggi') {
                prioritasEl.innerHTML = '<span class="badge bg-danger px-3 py-1">Urgent</span>';
            } else if (prioritas === 'Sedang') {
                prioritasEl.innerHTML = '<span class="badge bg-warning text-dark px-3 py-1">Sedang</span>';
            } else {
                prioritasEl.innerHTML = '<span class="badge bg-success px-3 py-1">Rendah</span>';
            }

            let alasanContainer = document.getElementById('alasan-container');
            let alasanTextarea = modalForm.querySelector('textarea[name="alasan_tidak_hadir"]');
            if (userRole === 'Kepala') {
                alasanContainer.classList.add('d-none');
                alasanTextarea.required = false;
                alasanTextarea.value = '';
            } else {
                alasanContainer.classList.remove('d-none');
                alasanTextarea.required = true;
                alasanTextarea.value = '';
            }

            let replacementNeeded = needsReplacementDisposisi();
            let disposisiContainer = document.getElementById('disposisi-pengganti-container');
            let selectPengganti = document.getElementById('select-pengganti');
            let helpPengganti = document.getElementById('disposisi-pengganti-help');
            let catatanPengganti = modalForm.querySelector('textarea[name="catatan_pengganti"]');

            disposisiContainer.classList.toggle('d-none', !replacementNeeded);
            selectPengganti.required = replacementNeeded;
            selectPengganti.innerHTML = '<option value="">Pilih pegawai</option>';
            catatanPengganti.value = '';

            if (replacementNeeded) {
                disposisiCandidates.forEach(function (pegawai) {
                    let option = document.createElement('option');
                    option.value = pegawai.nip;
                    option.textContent = [
                        pegawai.nama,
                        pegawai.jabatan,
                        pegawai.bidang
                    ].filter(Boolean).join(' - ');
                    selectPengganti.appendChild(option);
                });

                helpPengganti.textContent = userRole === 'Kepala'
                    ? 'Agenda hanya memiliki Anda sebagai peserta. Pilih Kabid atau Sekretaris sebagai pengganti.'
                    : userRole === 'Kabid'
                    ? 'Agenda hanya memiliki Anda sebagai peserta. Pilih Subkoor atau Staff pada bidang Anda sebagai pengganti.'
                    : 'Agenda hanya memiliki Anda sebagai peserta. Pilih Staff bawahan Anda sebagai pengganti.';
            }
        }

        // Fungsi untuk menutup popup
        function closePopup() {
            if (popupCard) popupCard.style.display = 'none';
            activeEventProps = null;
        }

        // Tutup popup saat klik tombol close
        if (popupCloseBtn) {
            popupCloseBtn.addEventListener('click', closePopup);
        }

        // Tutup popup saat klik di luar popup
        document.addEventListener('click', function (e) {
            if (popupCard && popupCard.style.display === 'block') {
                if (!popupCard.contains(e.target) && !e.target.closest('.fc-event')) {
                    closePopup();
                }
            }
        });

        let calendar = new FullCalendar.Calendar(calendarEl, {
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
                    <div class="${bgClass} w-100 h-100 d-flex flex-column" style="border-radius: 4px; padding: 5px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden; cursor: pointer;">
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

            // KLIK EVENT: Tampilkan popup card
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                info.jsEvent.stopPropagation();

                let event = info.event;
                let props = event.extendedProps;

                // Isi konten popup
                popupTitle.textContent = event.title;
                popupWaktu.textContent = props.waktu || '-';
                popupTanggal.textContent = props.tanggal_kegiatan || '-';
                popupLokasi.textContent = props.lokasi || '-';
                popupPerihal.textContent = props.perihal || event.title;

                // Simpan data event aktif
                activeEventProps = props;

                // Posisi popup tergantung view aktif
                let containerRect = calendarEl.closest('.container-fluid').getBoundingClientRect();
                let rect = info.el.getBoundingClientRect();
                let popupWidth = 320;
                let top, left;

                let currentView = calendar.view.type;

                if (currentView === 'timeGridDay') {
                    // VIEW HARI: popup di tengah horizontal, sejajar vertikal dengan event
                    let calBoxRect = calendarEl.closest('.border').getBoundingClientRect();
                    left = (calBoxRect.width / 2) - (popupWidth / 2) + (calBoxRect.left - containerRect.left);
                    top = rect.top - containerRect.top;
                } else {
                    // VIEW MINGGU/BULAN: popup di samping event yang diklik
                    top = rect.top - containerRect.top;
                    left = rect.right - containerRect.left + 10;

                    if (left + popupWidth > containerRect.width) {
                        left = rect.left - containerRect.left - popupWidth - 10;
                    }
                    if (top + 250 > containerRect.height) {
                        top = containerRect.height - 260;
                    }
                }
                if (top < 10) top = 10;

                popupCard.style.top = top + 'px';
                popupCard.style.left = left + 'px';
                popupCard.style.display = 'block';
            },

            datesSet: function (info) {
                const judulKalender = document.getElementById('judul-agenda');
                if (judulKalender) {
                    judulKalender.innerText = info.view.title;
                }
                // Tutup popup saat navigasi kalender
                closePopup();
            }
        });

        calendar.render();

        // TOMBOL "BATAL HADIR" di popup → buka modal atau langsung submit
        if (popupBatalBtn) {
            popupBatalBtn.addEventListener('click', function () {
                if (!activeEventProps) return;

                let idAgenda = activeEventProps.id_agenda;
                let formAction = '/agenda/' + idAgenda + '/batal-hadir';

                if (userRole === 'Kepala' && (!needsReplacementDisposisi() || hasAvailablePendamping())) {
                    // Kepala Kantor: langsung submit tanpa modal (tanpa alasan)
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = formAction;

                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]')?.content 
                                 || document.querySelector('input[name="_token"]')?.value || '';
                    form.appendChild(csrf);

                    document.body.appendChild(form);
                    form.submit();
                } else {
                    // Kabid ke bawah wajib alasan. Kepala juga memakai modal saat harus disposisi pengganti.
                    fillAttendanceModal(formAction);
                    // Tutup popup dan buka modal
                    closePopup();
                    let modal = new bootstrap.Modal(document.getElementById('tidakHadirModal'));
                    modal.show();
                }
            });
        }

        // KONTROL NAVIGATION PANAH
        const btnPrev = document.getElementById('btn-prev-agenda');
        const btnNext = document.getElementById('btn-next-agenda');
        if (btnPrev) btnPrev.addEventListener('click', () => { calendar.prev(); closePopup(); });
        if (btnNext) btnNext.addEventListener('click', () => { calendar.next(); closePopup(); });

        // KONTROL JENIS VIEW
        const btnHari = document.getElementById('btn-hari-agenda');
        const btnMinggu = document.getElementById('btn-minggu-agenda');
        const btnBulan = document.getElementById('btn-bulan-agenda');

        function resetBtnStyles() {
            [btnHari, btnMinggu, btnBulan].forEach(btn => {
                if (btn) {
                    btn.classList.remove('btn-white', 'fw-bold');
                    btn.classList.add('btn-light');
                    btn.style.borderWidth = '1px';
                }
            });
        }

        if (btnHari) btnHari.addEventListener('click', function () {
            calendar.changeView('timeGridDay');
            resetBtnStyles();
            this.classList.remove('btn-light');
            this.classList.add('btn-white', 'fw-bold');
            this.style.borderWidth = '2px';
            closePopup();
        });

        if (btnMinggu) btnMinggu.addEventListener('click', function () {
            calendar.changeView('timeGridWeek');
            resetBtnStyles();
            this.classList.remove('btn-light');
            this.classList.add('btn-white', 'fw-bold');
            this.style.borderWidth = '2px';
            closePopup();
        });

        if (btnBulan) btnBulan.addEventListener('click', function () {
            calendar.changeView('dayGridMonth');
            resetBtnStyles();
            this.classList.remove('btn-light');
            this.classList.add('btn-white', 'fw-bold');
            this.style.borderWidth = '2px';
            closePopup();
        });
    }
});
