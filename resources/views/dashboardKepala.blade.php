<x-layout :role="$role">
    <x-slot:title>{{ $title }}</x-slot:title>
    <x-dashboard-content 
        :jmlSurat="15" 
        :jmlNotif="5" 
        :jmlAgenda="2" 
    />
</x-layout>