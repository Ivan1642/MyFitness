<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFitness</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-[#003942] min-h-screen flex flex-col">

    {{-- Navbar / Header --}}
    @include('partials.navbar')

    {{-- Contenido principal --}}
    <main class="flex-1 p-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')
</body>
</html>