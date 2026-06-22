<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto fw-medium">
        <li class="nav-item mb-3">
            <x-navlink-fr href="/staff" :active="request()->is('staff')" icon="house">Dashboard <span class="badge rounded-pill bg-danger ms-2" id="nav-notif-badge" style="display:none">0</span></x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/surat_masuk" :active="request()->is('staff/surat_masuk')" icon="envelope">Surat Masuk</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/agenda" :active="request()->is('staff/agenda')" icon="calendar-event">Agenda</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/kalender_kantor" :active="request()->is('staff/kalender_kantor')" icon="calendar-check">Kalender Kantor</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/profil" :active="request()->is('*profil')" icon="person-circle">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
