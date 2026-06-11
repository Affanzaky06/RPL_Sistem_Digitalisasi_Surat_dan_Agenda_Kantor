<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <x-navlink-fr href="/kepegawaian" :active="request()->is('kepegawaian')">Dashboard</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/kepegawaian/input_data" :active="request()->is('kepegawaian/input_data')">Input Data Pegawai</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/kepegawaian/list" :active="request()->is('kepegawaian/list')">List Pegawai</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/kepegawaian/kalender_kantor" :active="request()->is('kepegawaian/kalender_kantor')">Kalender Kantor</x-navlink-fr>
        </li>
        <li>
            <x-navlink-fr href="/kepegawaian/profil" :active="request()->is('kepegawaian/profil')">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
