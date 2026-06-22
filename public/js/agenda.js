// file: public/js/agenda.js

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar-agenda');

    if (calendarEl) {
        let databaseEvents = JSON.parse(calendarEl.dataset.events || '[]');
        let userRole = calendarEl.dataset.role || '';

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
            slotMinTime: '00:00:00',
            slotMaxTime: '24:00:00',
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

                let now = new Date();
                let eventStart = event.start;
                let eventEnd = event.end || eventStart;
                let dynamicStatus = props.status;

                if (eventEnd && now > eventEnd) {
                    dynamicStatus = 'terlaksana';
                } else if (eventStart && eventEnd && now >= eventStart && now <= eventEnd) {
                    dynamicStatus = 'berlangsung';
                } else if (eventStart && now < eventStart) {
                    dynamicStatus = 'mendatang';
                }

                if (dynamicStatus === 'terlaksana') {
                    bgClass = 'bg-terlaksana'; badgeClass = 'badge-terlaksana'; badgeText = 'Terlaksana';
                } else if (dynamicStatus === 'berlangsung') {
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

                // Jangan munculkan popup jika agenda sudah terlaksana
                if (props.status === 'terlaksana') {
                    return;
                }

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

        // TOMBOL "BATAL HADIR" di popup
        if (popupBatalBtn) {
            popupBatalBtn.addEventListener('click', function () {
                if (!activeEventProps) return;

                let idAgenda = activeEventProps.id_agenda;
                let currentProps = activeEventProps;

                if (userRole === 'Kepala' || userRole === 'Kabid' || userRole === 'Subkoor' || userRole === 'Subkoordinator') {
                    // Cek pendamping dulu via AJAX
                    fetch('/agenda/' + idAgenda + '/cek-pendamping', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        closePopup();
                        showBatalModalWithDispo(idAgenda, data, currentProps);
                    })
                    .catch(err => {
                        console.error('Error cek pendamping:', err);
                        // Fallback: langsung batal hadir tanpa cek
                        submitBatalHadir(idAgenda);
                    });
                } else {
                    // Staff: tampilkan modal dengan form alasan (batal biasa)
                    closePopup();
                    showModalTolakAlasan(idAgenda, currentProps);
                }
            });
        }

        /**
         * Tampilkan form alasan tidak hadir (fallback batal biasa)
         */
        function showModalTolakAlasan(idAgenda, eventProps) {
            let formAction = '/agenda/' + idAgenda + '/batal-hadir';
            let modalForm = document.getElementById('form-tidak-hadir');
            modalForm.action = formAction;

            // Isi data modal
            document.getElementById('modal-pengirim').textContent = eventProps.pengirim || '-';
            document.getElementById('modal-nomor-surat').textContent = eventProps.nomor_surat || '-';
            document.getElementById('modal-perihal').textContent = eventProps.perihal || '-';
            document.getElementById('modal-tanggal-surat').textContent = eventProps.tanggal_surat || '-';
            document.getElementById('modal-waktu').textContent = eventProps.waktu || '-';

            // Prioritas badge
            let prioritasEl = document.getElementById('modal-prioritas');
            let prioritas = eventProps.prioritas || 'Rendah';
            if (prioritas === 'Tinggi') {
                prioritasEl.innerHTML = '<span class="badge bg-danger px-3 py-1">Urgent</span>';
            } else if (prioritas === 'Sedang') {
                prioritasEl.innerHTML = '<span class="badge bg-warning text-dark px-3 py-1">Sedang</span>';
            } else {
                prioritasEl.innerHTML = '<span class="badge bg-success px-3 py-1">Rendah</span>';
            }

            // Reset textarea
            modalForm.querySelector('textarea[name="alasan_tidak_hadir"]').value = '';

            let modal = new bootstrap.Modal(document.getElementById('tidakHadirModal'));
            modal.show();
        }

        /**
         * Tampilkan modal khusus saat batal hadir (Kepala/Kabid/Subkoor)
         */
        function showBatalModalWithDispo(idAgenda, data, eventProps) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value || '';

            // JIKA MURNI SEBAGAI PENDAMPING (Bukan Penerima Disposisi Utama/Perwakilan)
            // Langsung tampilkan form alasan tolak (tidak boleh disposisi/wakilkan lagi)
            if (data.is_pendamping_only) {
                showModalTolakAlasan(idAgenda, eventProps);
                return;
            }

            if (data.ada_pendamping) {
                // ADA PENDAMPING → Tampilkan modalKonfirmasiPendamping
                let listContainer = document.getElementById('list-pendamping-konfirmasi');
                let html = '<hr><p class="mb-2 text-muted small">Pilih pendamping yang akan menjadi perwakilan:</p>';
                data.pendamping.forEach(function(p) {
                    html += `
                        <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <div class="fw-bold">${p.nama}</div>
                                <small class="text-muted">${p.jabatan} | ${p.bidang}</small>
                                <br><small class="badge ${p.status === 'Hadir' ? 'bg-success' : 'bg-warning text-dark'}">${p.status}</small>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <form action="/agenda/${idAgenda}/wakilkan" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="nip_perwakilan" value="${p.nip}">
                                    <button type="submit" class="btn btn-primary btn-sm" style="width:140px;">
                                        <i class="bi bi-arrow-repeat me-1"></i> Wakilkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    `;
                });
                listContainer.innerHTML = html;
                listContainer.style.display = 'none'; // Sembunyikan list pendamping awalnya

                let modalKonfirmasi = new bootstrap.Modal(document.getElementById('modalKonfirmasiPendamping'));
                
                // Tombol "Disposisikan ke Pendamping"
                let btnShowPendamping = document.getElementById('btn-show-pendamping');
                let newBtnShow = btnShowPendamping.cloneNode(true);
                btnShowPendamping.parentNode.replaceChild(newBtnShow, btnShowPendamping);
                newBtnShow.addEventListener('click', function() {
                    listContainer.style.display = 'block'; // Tampilkan list pendamping
                });

                // Tombol "Batalkan Semua Agenda dan Buat Dispo Ulang Biasa"
                let btnBatalkanSemua = document.getElementById('btn-batalkan-semua-agenda');
                let newBtnBatalSemua = btnBatalkanSemua.cloneNode(true);
                btnBatalkanSemua.parentNode.replaceChild(newBtnBatalSemua, btnBatalkanSemua);
                newBtnBatalSemua.addEventListener('click', function() {
                    modalKonfirmasi.hide();
                    showModalDisposisiBatal(idAgenda, data, eventProps);
                });

                // Tombol "Batal dan Kirim Alasan" (Khusus non-Kepala)
                let fallbackButtons = document.getElementById('fallback-buttons');
                let btnTolakKirimAlasan = document.getElementById('btn-tolak-kirim-alasan');
                if (btnTolakKirimAlasan && fallbackButtons) {
                    if (userRole === 'Kepala') {
                        fallbackButtons.style.display = 'none';
                    } else {
                        fallbackButtons.style.display = 'block';
                        let newBtnTolak = btnTolakKirimAlasan.cloneNode(true);
                        btnTolakKirimAlasan.parentNode.replaceChild(newBtnTolak, btnTolakKirimAlasan);
                        newBtnTolak.addEventListener('click', function() {
                            modalKonfirmasi.hide();
                            showModalTolakAlasan(idAgenda, eventProps);
                        });
                    }
                }

                modalKonfirmasi.show();
            } else {
                // JIKA TIDAK ADA PENDAMPING
                // 1. Kepala: Langsung tampilkan form disposisi ke bawahan (Sesuai instruksi user)
                if (userRole === 'Kepala') {
                    showModalDisposisiBatal(idAgenda, data, eventProps);
                } else {
                    // 2. Kabid & Subkoor: Tawarkan opsi Tolak (Tulis Alasan) atau Disposisikan
                    let modalPilih = new bootstrap.Modal(document.getElementById('modalPilihAksiBatal'));

                    let btnDisposisi = document.getElementById('btn-aksi-disposisi');
                    let newBtnDisposisi = btnDisposisi.cloneNode(true);
                    btnDisposisi.parentNode.replaceChild(newBtnDisposisi, btnDisposisi);
                    newBtnDisposisi.addEventListener('click', function() {
                        modalPilih.hide();
                        showModalDisposisiBatal(idAgenda, data, eventProps);
                    });

                    let btnTolak = document.getElementById('btn-aksi-tolak');
                    let newBtnTolak = btnTolak.cloneNode(true);
                    btnTolak.parentNode.replaceChild(newBtnTolak, btnTolak);
                    newBtnTolak.addEventListener('click', function() {
                        modalPilih.hide();
                        showModalTolakAlasan(idAgenda, eventProps);
                    });

                    modalPilih.show();
                }
            }
        }

        function showModalDisposisiBatal(idAgenda, data, eventProps) {
            let form = document.getElementById('formDisposisiBatal');
            form.action = `/agenda/${idAgenda}/disposisi-batal`;

            // Isi detail surat dari eventProps (lebih aman karena selalu ada dari FullCalendar)
            if (eventProps) {
                document.getElementById('dispo-batal-pengirim').textContent = eventProps.pengirim || '-';
                document.getElementById('dispo-batal-nomor').textContent = eventProps.nomor_surat || '-';
                document.getElementById('dispo-batal-perihal').textContent = eventProps.perihal || '-';
                document.getElementById('dispo-batal-tanggal').textContent = eventProps.tanggal_surat || '-';
                document.getElementById('dispo-batal-jenis').textContent = eventProps.jenis_surat || '-';
                
                let prioritasEl = document.getElementById('dispo-batal-prioritas');
                let prio = eventProps.prioritas || 'Rendah';
                if (prio === 'Tinggi') prioritasEl.innerHTML = '<span class="badge bg-danger px-3 py-1">Tinggi</span>';
                else if (prio === 'Sedang') prioritasEl.innerHTML = '<span class="badge bg-warning text-dark px-3 py-1">Sedang</span>';
                else prioritasEl.innerHTML = '<span class="badge bg-success px-3 py-1">Rendah</span>';
            }

            let modalDispo = new bootstrap.Modal(document.getElementById('modalDisposisiBatal'));
            modalDispo.show();
        }

        // SEARCH & FILTER UNTUK MODAL DISPOSISI BATAL
        const searchInputBatal = document.querySelector('.search-penerima-batal');
        const filterSelectBatal = document.querySelector('.filter-jabatan-penerima-batal');
        const listContainerBatal = document.getElementById('list-penerima-batal');

        function filterListBatal() {
            if (!searchInputBatal || !filterSelectBatal || !listContainerBatal) return;
            const keyword = searchInputBatal.value.toLowerCase().trim();
            const jabatan = filterSelectBatal.value;

            listContainerBatal.querySelectorAll('.penerima-item-batal').forEach(item => {
                const nama = item.dataset.nama || '';
                const itemJab = item.dataset.jabatan || '';

                const matchNama = keyword === '' || nama.includes(keyword);
                const matchJab = jabatan === 'ALL' || itemJab === jabatan;

                if (matchNama && matchJab) {
                    item.style.setProperty('display', 'flex', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        }

        if (searchInputBatal) searchInputBatal.addEventListener('input', filterListBatal);
        if (filterSelectBatal) filterSelectBatal.addEventListener('change', filterListBatal);

        /**
         * Fallback: langsung submit batal hadir
         */
        function submitBatalHadir(idAgenda) {
            let formAction = '/agenda/' + idAgenda + '/batal-hadir';
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