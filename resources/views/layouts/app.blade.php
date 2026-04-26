<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MyFitness</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <header class="bg-gray-900 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center p-4">

            <!-- Logo -->
            <div class="text-xl font-bold">
                MyFitness
            </div>

            <!-- Menu -->
            <nav class="hidden md:flex space-x-6">
                <a href="/" class="hover:text-gray-300">Inicio</a>
                <a href="/rutinas" class="hover:text-gray-300">Rutinas</a>
                <a href="/progreso" class="hover:text-gray-300">Progreso</a>
                <a href="/feed" class="hover:text-gray-300">Feed</a>
            </nav>

            <!-- Usuario -->
            <div class="flex items-center space-x-4">

                <!-- Si no está logueado -->
                <a href="/login" class="bg-white text-black px-3 py-1 rounded hover:bg-gray-200">
                    Login
                </a>

                <a href="/register" class="border border-white px-3 py-1 rounded hover:bg-white hover:text-black">
                    Registro
                </a>

                <!-- Cuando está logueado -->
                <div class="relative">
                    <button class="flex items-center space-x-2">
                        <span>Usuario</span>
                        <img src="/img/user.png" class="w-8 h-8 rounded-full">
                    </button>
                </div>
            </div>

        </div>
    </header>

    <main class="p-4">
        @yield('content')
    </main>

</body>
</html>