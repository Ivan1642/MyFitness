<header id="main-header"
    class="text-[#003942] bg-white shadow-md fixed top-0 left-0 w-full z-50 transition-all duration-300">

    <div class="container mx-auto flex justify-between items-center p-2">

        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center">
            <img src="{{ asset('img/logoMyFitness2.png') }}" class="h-20 w-auto" alt="MyFitness">
        </a>

        @auth
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('dashboard') }}" class="hover:text-black hover:underline">Inicio</a>
                <a href="{{ route('routines.index') }}" class="hover:text-black hover:underline">Rutinas</a>
                <a href="{{ route('progress.index') }}" class="hover:text-black hover:underline">Progreso</a>
                <a href="{{ route('feed') }}" class="hover:text-black hover:underline">Feed</a>
                <a href="" class="hover:text-black hover:underline">Notificaciones</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.index') }}" class="hover:text-black hover:underline">Panel de Admin</a>
                @endif
            </nav>
        @endauth

        <div class="flex items-center space-x-4">

            @guest
                <a href="{{ route('login') }}" class="border text-white px-3 py-1 rounded hover:text-[#003942] font-bold bg-[#003942] hover:bg-white transition">
                    Iniciar Sesión
                </a>

                <a href="{{ route('register') }}" class="border border-[#003942] px-3 py-1 rounded hover:bg-[#003942] hover:text-white font-bold transition">
                    Registro
                </a>
            @endguest

            @auth
                <div class="relative" x-data="{ open: false }">

                    <button @click="open = !open" class="flex items-center space-x-2 cursor-pointer">

                        <span>{{ auth()->user()->name }}</span>

                        <div class="h-16 w-16 rounded-full overflow-hidden relative flex items-center justify-center">
                            <img
                                src="{{ auth()->user()->avatar
                                    ? asset('storage/' . auth()->user()->avatar)
                                    : asset('img/predeterminada_perfil.png') }}"
                                class="absolute inset-0 h-full w-full object-cover object-center scale-[101%]"
                                alt="Avatar">
                        </div>

                    </button>

                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white text-[#003942] rounded shadow-lg z-50">

                        <a href="{{ route('dashboard') }}" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Inicio
                        </a>
                        <a href="{{ route('routines.index') }}" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Rutinas
                        </a>
                        <a href="{{ route('progress.index') }}" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Progreso
                        </a>
                        <a href="{{ route('feed') }}" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Feed
                        </a>
                        <a href="" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Notificaciones
                        </a>
                        <a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            Perfil
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.index') }}" class="block md:hidden px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                Panel de Admin
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                Cerrar Sesión
                            </button>
                        </form>

                    </div>

                </div>
            @endauth

        </div>

    </div>

</header>

<div class="h-24"></div>