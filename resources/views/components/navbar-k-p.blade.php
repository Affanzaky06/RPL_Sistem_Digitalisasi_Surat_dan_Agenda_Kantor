<nav class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary" style="width: 280px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <x-navlink-fr href="/kepala" :active="request()->is('kepala')" icon="home">
                Dashboard Kepala
            </x-navlink-fr>
        </li>
        <li> 
            <x-navlink-fr href="/kepala/surat_masuk" :active="request()->is('kepala/surat_masuk')" icon="envelope">
                Surat Masuk & Disposisi
            </x-navlink-fr>
         </li>
        <li>
            <x-navlink-fr href="/kepala/agenda" :active="request()->is('kepala/agenda')" icon="check2-square">
                Agenda
            </x-navlink-fr>
        </li>
        <li> 
            <x-navlink-fr href="/kepala/Laporan_Pemantauan" :active="request()->is('kepala/Laporan_Pemantauan')" icon="calendar-event">
                Laporan & Pemantauan
            </x-navlink-fr>
        </li>
         <li> 
            <x-navlink-fr href="/kepala/kalender_kantor" :active="request()->is('kepala/kalender_kantor')" icon="calendar-event">
                Kalender Kantor
            </x-navlink-fr>
        </li>
        <li> 
            <x-navlink-fr href="/kepala/profil" :active="request()->is('kepala/profil')" icon="people-circle">
                Profil
            </x-navlink-fr>
        </li>
    </ul>
</nav>