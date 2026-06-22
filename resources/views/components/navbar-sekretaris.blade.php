<nav class="d-flex flex-column flex-shrink-0 py-3 px-2 bg-body-tertiary" style="width: 270px;">

    <ul class="nav nav-pills flex-column mb-auto fw-medium">

        <li class="mb-3">
            <x-navlink-fr href="/sekretaris" :active="request()->is('sekretaris')" icon="house">

                Dashboard <span class="badge rounded-pill bg-danger ms-2" id="nav-notif-badge" style="display:none">0</span>

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="/sekretaris/verifikasi_surat" :active="request()->is('sekretaris/verifikasi_surat')" icon="check2-square">

                Verifikasi Surat

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="/sekretaris/agenda" :active="request()->is('sekretaris/agenda')" icon="calendar-event">

                Agenda

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="/sekretaris/kalender_kantor" :active="request()->is('sekretaris/kalender_kantor')" icon="calendar-check">

                Kalender Kantor

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="{{ route('sekretaris.riwayat') }}" :active="request()->is('sekretaris/riwayat_verifikasi')" icon="clock-history">

                Riwayat Verifikasi

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="{{ route('sekretaris.disposisi') }}" :active="request()->is('sekretaris/disposisi')" icon="envelope-paper">

                Surat Masuk & Disposisi

            </x-navlink-fr>
        </li>

        <li class="mb-3">
            <x-navlink-fr href="/sekretaris/profil" :active="request()->is('*profil')" icon="person-circle">

                Profil

            </x-navlink-fr>
        </li>

    </ul>

</nav>
