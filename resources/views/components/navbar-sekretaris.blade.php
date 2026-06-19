<nav class="d-flex flex-column flex-shrink-0 py-3 px-2 bg-body-tertiary" style="width: 250px;">

    <ul class="nav nav-pills flex-column mb-auto">

        <li class="mb-2">
            <x-navlink-fr href="/sekretaris" :active="request()->is('sekretaris')">

                Dashboard

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="/sekretaris/verifikasi_surat" :active="request()->is('sekretaris/verifikasi_surat')">

                Verifikasi Surat

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="/sekretaris/agenda" :active="request()->is('sekretaris/agenda')">

                Agenda

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="/sekretaris/kalender_kantor" :active="request()->is('sekretaris/kalender_kantor')">

                Kalender Kantor

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="{{ route('sekretaris.riwayat') }}" :active="request()->is('sekretaris/riwayat_verifikasi')">

                Riwayat Verifikasi

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="{{ route('sekretaris.disposisi') }}" :active="request()->is('sekretaris/disposisi')">

                Surat Masuk & Disposisi

            </x-navlink-fr>
        </li>

        <li class="mb-2">
            <x-navlink-fr href="/sekretaris/profil" :active="request()->is('*profil')">

                Profil

            </x-navlink-fr>
        </li>

    </ul>

</nav>
