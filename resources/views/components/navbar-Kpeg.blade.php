<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 240px;">
    <ul class="nav nav-pills flex-column mb-auto fw-medium">
        <li class="nav-item mb-3">
            <x-navlink-fr href="/kepegawaian" :active="request()->is('kepegawaian')" icon="house">Dashboard</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/kepegawaian/input_data" :active="request()->is('kepegawaian/input_data')" icon="person-plus">Input Data Pegawai</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/kepegawaian/list" :active="request()->is('kepegawaian/list')" icon="people">List Pegawai</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="{{ route('kepegawaian.bidang') }}" :active="request()->is('kepegawaian/bidang')" icon="diagram-3">Data Bidang</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/kepegawaian/kalender_kantor" :active="request()->is('kepegawaian/kalender_kantor')" icon="calendar-check">Kalender Kantor</x-navlink-fr>
        </li>
        <li class="mb-3">
            <x-navlink-fr href="/kepegawaian/profil" :active="request()->is('*profil')" icon="person-circle">Profil</x-navlink-fr>
        </li>
    </ul>
</nav>
