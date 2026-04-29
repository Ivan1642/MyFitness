<header id="main-header"
    class="text-[#003942] bg-white shadow-md fixed top-0 left-0 w-full z-50 transition-all duration-300">

    <div class="container mx-auto flex justify-between items-center p-2">

        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center">
            <img src="{{ asset('img/logoMyFitness2.png') }}" class="h-20 w-auto" alt="MyFitness">
        </a>

        @auth
            <nav class="hidden md:flex space-x-6">
                <a href="/" class="hover:text-white/70">Inicio</a>
                <a href="/rutinas" class="hover:text-white/70">Rutinas</a>
                <a href="/progreso" class="hover:text-white/70">Progreso</a>
                <a href="/feed" class="hover:text-white/70">Feed</a>
            </nav>
        @endauth

        <div class="flex items-center space-x-4">

            @guest
                <a href="{{ url('/login') }}" class="border text-white px-3 py-1 rounded hover:text-[#003942] font-bold bg-[#003942] hover:bg-white transition">
                    Iniciar Sesión
                </a>

                <a href="{{ url('/register') }}" class="border border-[#003942] px-3 py-1 rounded hover:bg-[#003942] hover:text-white font-bold transition">
                    Registro
                </a>
            @endguest

            @auth
                <div class="relative" x-data="{ open: false }">

                    <button @mouseenter="open = true"
                        @mouseleave="open = false"
                        class="flex items-center space-x-2">

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

                    <div x-show="open" @mouseenter="open = true"
                        @mouseleave="open = false" x-transition
                        class="absolute right-0 mt-2 w-40 bg-white text-[#003942] rounded shadow-lg z-50">

                        <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">
                            Perfil
                        </a>

                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>
            @endauth

        </div>

    </div>

</header>

<div class="h-24"></div>

<script>
const header = document.getElementById('main-header');

window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
        header.classList.add(
            'bg-[#003942]/95',
            'backdrop-blur-md',
            'shadow-lg'
        );
    } else {
        header.classList.remove(
            'bg-[#003942]/95',
            'backdrop-blur-md',
            'shadow-lg'
        );
    }
});
</script>