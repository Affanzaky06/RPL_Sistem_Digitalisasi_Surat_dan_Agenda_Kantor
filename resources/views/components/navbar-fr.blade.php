<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <x-navlink-fr href="/frontliner" :active="request()->is('frontliner')">Dashboard</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/frontliner/input_surat" :active="request()->is('frontliner/input_surat')">Input Surat</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/frontliner/riwayat_input" :active="request()->is('frontliner/riwayat_input')">Riwayat Input</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/frontliner/kalender_kantor" :active="request()->is('frontliner/kalender_kantor')">Kalender Kantor</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/frontliner/profil" :active="request()->is('*profil')">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
