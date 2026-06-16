@props(['role'])

@php
    // Mengubah huruf 'Kepala'/'Kabid' menjadi huruf kecil semua ('kepala'/'kabid') untuk keperluan URL
    $url = strtolower($role);
@endphp

<nav class="d-flex flex-column flex-shrink-0 py-3 px-2 bg-body-tertiary" style="width: 270px;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <x-navlink-fr href="/{{ $url }}" :active="request()->is($url)" icon="home">
                Dashboard {{ $url }}
            </x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/{{ $url }}/surat_masuk" :active="request()->is($url . '/surat_masuk')" icon="envelope">
                Surat Masuk & Disposisi
            </x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/{{ $url }}/agenda" :active="request()->is($url . '/agenda')" icon="check2-square">
                Agenda
            </x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/{{ $url }}/Laporan_Pemantauan" :active="request()->is($url . '/Laporan_Pemantauan')" icon="calendar-event">
                Laporan & Pemantauan
            </x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/{{ $url }}/kalender_kantor" :active="request()->is($url . '/kalender_kantor')" icon="calendar-event">
                Kalender Kantor
            </x-navlink-fr>
        </li>
        <li class="mb-2">
            <x-navlink-fr href="/{{ $url }}/profil" :active="request()->is('*profil')" icon="people-circle">
                Profil
            </x-navlink-fr>
        </li>
    </ul>
</nav>
