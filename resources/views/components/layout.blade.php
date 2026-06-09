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

        @if ($role === 'Kepala' || $role === 'Kabid' || $role === 'Subkoor')
            <x-navbar-k-p :role="$role" />
        @elseif($role === 'Staff')
            <x-navbar-staff />
        @elseif($role === 'Sekretaris')
            <x-navbar-sekretaris />
        @else
            <x-navbar-fr />
        @endif

        <main class="flex-grow-1 px-4 py-3 overflow-auto">
            {{ $slot }}

        </main>

    </div>

</body>

</html>
