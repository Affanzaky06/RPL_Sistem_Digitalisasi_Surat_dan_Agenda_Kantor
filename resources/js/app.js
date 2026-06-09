//
// import './bootstrap'; // Ini bawaan Laravel
import 'bootstrap';   // Ini memanggil Javascript milik UI Bootstrap

const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const weekDayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
const monthNames = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

const toDateKey = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const parseDateKey = (dateKey) => {
    const [year, month, day] = dateKey.split('-').map(Number);

    return new Date(year, month - 1, day);
};

const addDays = (date, days) => {
    const nextDate = new Date(date);
    nextDate.setDate(nextDate.getDate() + days);

    return nextDate;
};

const addMonths = (date, months) => {
    const nextDate = new Date(date);
    nextDate.setMonth(nextDate.getMonth() + months);

    return nextDate;
};

const startOfWeek = (date) => {
    const weekStart = new Date(date);
    const day = weekStart.getDay();
    const diff = day === 0 ? -6 : 1 - day;
    weekStart.setDate(weekStart.getDate() + diff);

    return weekStart;
};

const startOfMonthGrid = (date) => {
    const monthStart = new Date(date.getFullYear(), date.getMonth(), 1);

    return startOfWeek(monthStart);
};

const minuteOffset = (time) => {
    const [hour, minute] = time.split(':').map(Number);

    return (hour - 9) * 59 + (minute / 60) * 59;
};

const eventTime = (event) => `${event.start}-${event.end}`;

const escapeText = (value) => {
    const div = document.createElement('div');
    div.textContent = value ?? '';

    return div.innerHTML;
};

const eventCard = (event) => {
    const top = Math.max(0, minuteOffset(event.start));
    const height = Math.max(54, minuteOffset(event.end) - minuteOffset(event.start));
    const eventId = escapeText(event.id ?? `${event.date}-${event.start}-${event.title}`);

    return `
        <article class="agenda-event agenda-event-${escapeText(event.tone)}" style="top: ${top}px; height: ${height}px;" data-agenda-event="${eventId}">
            <div class="agenda-event-time">
                <span>${escapeText(eventTime(event))}</span>
                <i class="bi bi-clock"></i>
            </div>
            <strong>${escapeText(event.title)}</strong>
            <p>
                <i class="bi bi-geo-alt"></i>
                ${escapeText(event.place)}
            </p>
            ${event.owner ? `<small class="agenda-event-owner">${escapeText(event.owner)}</small>` : ''}
            <span class="agenda-event-status">${escapeText(event.status)}</span>
        </article>
    `;
};

const emptyState = () => '<p class="agenda-empty">Belum ada agenda pada periode ini.</p>';

const renderAgenda = (calendar) => {
    const content = calendar.querySelector('[data-agenda-content]');
    const summary = calendar.querySelector('[data-agenda-summary]');
    const popover = calendar.querySelector('[data-agenda-popover]');
    const title = calendar.querySelector('[data-agenda-title]');
    const buttons = calendar.querySelectorAll('[data-agenda-view]');
    const { events, view, activeDate } = calendar.agendaState;
    const dateKey = toDateKey(activeDate);
    const weekStart = startOfWeek(activeDate);
    const owner = calendar.dataset.owner;

    buttons.forEach((button) => {
        const isActive = button.dataset.agendaView === view;
        button.classList.toggle('active', isActive);
        button.classList.toggle('btn-outline-dark', isActive);
        button.classList.toggle('btn-outline-secondary', !isActive);
    });

    if (view === 'day') {
        const dayEvents = events.filter((event) => event.date === dateKey);
        title.textContent = `${dayNames[activeDate.getDay()]}, ${activeDate.getDate()} ${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;
        content.innerHTML = `
            <div class="agenda-days agenda-days-single">
                <div></div>
                <div>${dayNames[activeDate.getDay()]}</div>
            </div>
            <div class="agenda-grid agenda-grid-day">
                ${renderTimes()}
                <div class="agenda-day-column">${dayEvents.map(eventCard).join('') || emptyState()}</div>
            </div>
        `;
        renderSummary(summary, dayEvents, owner);
        bindEventDetails(calendar);
        hidePopover(popover);
        return;
    }

    if (view === 'month') {
        const monthStart = startOfMonthGrid(activeDate);
        const dates = Array.from({ length: 42 }, (_, index) => addDays(monthStart, index));
        const monthEvents = events.filter((event) => {
            const eventDate = parseDateKey(event.date);

            return eventDate.getMonth() === activeDate.getMonth() && eventDate.getFullYear() === activeDate.getFullYear();
        });

        title.textContent = `${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;
        content.innerHTML = `
            <div class="agenda-month-days">
                ${weekDayNames.map((day) => `<div>${day}</div>`).join('')}
            </div>
            <div class="agenda-month-grid">
                ${dates.map((date) => renderMonthCell(date, activeDate, events)).join('')}
            </div>
        `;
        renderSummary(summary, monthEvents, owner);
        bindEventDetails(calendar);
        hidePopover(popover);
        return;
    }

    const weekDates = Array.from({ length: 7 }, (_, index) => addDays(weekStart, index));
    const weekEnd = addDays(weekStart, 6);
    const weekEvents = events.filter((event) => {
        const eventDate = parseDateKey(event.date);

        return eventDate >= weekStart && eventDate <= weekEnd;
    });

    title.textContent = `Minggu ${Math.ceil(activeDate.getDate() / 7)} - ${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;
    content.innerHTML = `
        <div class="agenda-days">
            <div></div>
            ${weekDayNames.map((day) => `<div>${day}</div>`).join('')}
        </div>
        <div class="agenda-grid">
            ${renderTimes()}
            ${weekDates.map((date) => {
                const eventsInDay = events.filter((event) => event.date === toDateKey(date));
                return `<div class="agenda-day-column" aria-label="${dayNames[date.getDay()]}">${eventsInDay.map(eventCard).join('')}</div>`;
            }).join('')}
            <div class="agenda-scroll-indicator" aria-hidden="true"></div>
        </div>
    `;
    renderSummary(summary, weekEvents, owner);
    bindEventDetails(calendar);
    hidePopover(popover);
};

const renderTimes = () => {
    const times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

    return `<div class="agenda-times">${times.map((time) => `<span>${time}</span>`).join('')}</div>`;
};

const renderMonthCell = (date, activeDate, events) => {
    const dateEvents = events.filter((event) => event.date === toDateKey(date));
    const isOutside = date.getMonth() !== activeDate.getMonth();

    return `
        <div class="agenda-month-cell ${isOutside ? 'is-outside' : ''}">
            <span>${date.getDate()}</span>
            ${dateEvents.slice(0, 2).map((event) => `<p class="agenda-month-event agenda-month-event-${escapeText(event.tone)}">${escapeText(event.title)}</p>`).join('')}
            ${dateEvents.length > 2 ? `<small>+${dateEvents.length - 2} agenda</small>` : ''}
        </div>
    `;
};

const renderSummary = (summary, events, owner) => {
    if (!summary) {
        return;
    }

    summary.innerHTML = events.slice(0, 3).map((event, index) => `
        <article class="agenda-summary-card">
            <strong>Meeting ${index + 1}: ${escapeText(event.title)}</strong>
            <p class="mb-1">${escapeText(event.date)} | ${escapeText(eventTime(event))}</p>
            <p class="mb-0">Peserta: ${escapeText(event.participants || owner)}</p>
        </article>
    `).join('') || '<p class="agenda-summary-empty">Belum ada ringkasan agenda.</p>';
};

const bindEventDetails = (calendar) => {
    const popover = calendar.querySelector('[data-agenda-popover]');

    if (!popover) {
        return;
    }

    calendar.querySelectorAll('[data-agenda-event]').forEach((card) => {
        card.addEventListener('click', () => {
            const event = calendar.agendaState.events.find((item) => {
                const eventId = item.id ?? `${item.date}-${item.start}-${item.title}`;

                return String(eventId) === card.dataset.agendaEvent;
            });

            if (!event || event.tone === 'blocked') {
                return;
            }

            showPopover(popover, event, card);
        });
    });
};

const hidePopover = (popover) => {
    if (!popover) {
        return;
    }

    popover.classList.add('d-none');
    popover.innerHTML = '';
};

const formatLongDate = (dateKey) => {
    const date = parseDateKey(dateKey);

    return `${dayNames[date.getDay()]}, ${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
};

const showPopover = (popover, event, card) => {
    const cardBox = card.getBoundingClientRect();
    const calendarBox = popover.parentElement.getBoundingClientRect();

    popover.innerHTML = `
        <div class="agenda-popover-head">
            <i class="bi bi-calendar3"></i>
            <button type="button" aria-label="Tutup detail agenda" data-agenda-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <h3>${escapeText(event.title)}</h3>
        <p><i class="bi bi-clock"></i><span>${escapeText(eventTime(event))}</span></p>
        <p><i class="bi bi-calendar3"></i><span>${escapeText(formatLongDate(event.date))}</span></p>
        <p><i class="bi bi-geo-alt"></i><span>${escapeText(event.place)}</span></p>
        <hr>
        <strong>Agenda</strong>
        <p class="agenda-popover-note">${escapeText(event.note || 'Membahas koordinasi dan tindak lanjut agenda.')}</p>
        <button type="button" class="btn btn-outline-danger agenda-cancel-button">
            <i class="bi bi-x-lg"></i>
            Batal Hadir
        </button>
    `;

    popover.style.left = `${Math.min(cardBox.right - calendarBox.left + 10, calendarBox.width - 285)}px`;
    popover.style.top = `${Math.max(88, cardBox.top - calendarBox.top - 60)}px`;
    popover.classList.remove('d-none');

    popover.querySelector('[data-agenda-close]').addEventListener('click', () => hidePopover(popover));
};

document.querySelectorAll('[data-agenda-calendar]').forEach((calendar) => {
    const events = JSON.parse(calendar.dataset.events || '[]');
    const initialDate = events[0]?.date ? parseDateKey(events[0].date) : new Date();
    const filter = calendar.querySelector('[data-agenda-filter]');

    calendar.agendaState = {
        activeDate: initialDate,
        allEvents: events,
        events,
        view: 'week',
    };

    calendar.querySelector('[data-agenda-prev]').addEventListener('click', () => {
        const { view, activeDate } = calendar.agendaState;
        const diff = view === 'day' ? -1 : -7;
        calendar.agendaState.activeDate = view === 'month' ? addMonths(activeDate, -1) : addDays(activeDate, diff);
        renderAgenda(calendar);
    });

    calendar.querySelector('[data-agenda-next]').addEventListener('click', () => {
        const { view, activeDate } = calendar.agendaState;
        const diff = view === 'day' ? 1 : 7;
        calendar.agendaState.activeDate = view === 'month' ? addMonths(activeDate, 1) : addDays(activeDate, diff);
        renderAgenda(calendar);
    });

    calendar.querySelectorAll('[data-agenda-view]').forEach((button) => {
        button.addEventListener('click', () => {
            calendar.agendaState.view = button.dataset.agendaView;
            renderAgenda(calendar);
        });
    });

    if (filter) {
        filter.addEventListener('change', () => {
            const selectedGroup = filter.value;
            calendar.dataset.owner = filter.options[filter.selectedIndex].text;
            calendar.agendaState.events = selectedGroup === 'all'
                ? calendar.agendaState.allEvents
                : calendar.agendaState.allEvents.filter((event) => event.group === selectedGroup || event.group === 'all');
            renderAgenda(calendar);
        });
    }

    renderAgenda(calendar);
});
