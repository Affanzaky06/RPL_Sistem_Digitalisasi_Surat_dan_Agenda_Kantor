<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto fw-medium">
        <li class="nav-item mb-2">
            <x-navlink-fr href="/frontliner" :active="request()->is('frontliner')" icon="house">Dashboard</x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/frontliner/input_surat" :active="request()->is('frontliner/input_surat')" icon="pencil-square">Input Surat</x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/frontliner/riwayat_input" :active="request()->is('frontliner/riwayat_input')" icon="clock-history">Riwayat Input</x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/frontliner/kalender_kantor" :active="request()->is('frontliner/kalender_kantor')" icon="calendar-check">Kalender Kantor</x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/frontliner/profil" :active="request()->is('*profil')" icon="person-circle">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
