<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <x-dashboard-fr-skr
        :jmlSurat="$jmlSurat" :jmltolak="$jmltolak" :TungguVeriv="$TungguVeriv"
        :ringkasanAgenda="$ringkasanAgenda"
    >
    </x-dashboard-fr-skr>
</x-layout>