@props(['role' => 'frontliner'])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <title>Dasboard</title>
</head>


<body class="vh-100 d-flex flex-column overflow-hidden ">

    <x-header>
        Sistem Agenda Digital - {{ $title }}
    </x-header>

    <div class="d-flex flex-grow-1 overflow-hidden">
        
        @if($role === 'Kepala' || $role === 'Kabid' || $role === 'Subkoor')
            <x-navbar-k-p :role="$role"></x-navbar-k-p>
        @elseif($role === 'Staff')
            <x-navbar-staff></x-navbar-staff>
        @else
            <x-navbar-fr></x-navbar-fr>
        @endif

        <main class="flex-grow-1 p-4 overflow-auto">
            <h2>{{ $slot }}</h2>

        </main>

    </div>

</body>

</html>