<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto fw-medium">
        <li class="nav-item mb-3">
            <x-navlink-fr href="/staff" :active="request()->is('staff')">Dashboard</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/surat_masuk" :active="request()->is('staff/surat_masuk')">Surat Masuk</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/agenda" :active="request()->is('staff/agenda')">Agenda</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/kalender_kantor" :active="request()->is('staff/kalender_kantor')">Kalender Kantor</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/staff/profil" :active="request()->is('*profil')">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
