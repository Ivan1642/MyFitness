<header id="main-header"
    class="bg-[#003942] text-white shadow-md fixed top-0 left-0 w-full z-50 transition-all duration-300">

    <div class="container mx-auto flex justify-between items-center p-2">

        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('img/logoMyFitness.png') }}" class="h-20 w-auto" alt="MyFitness">
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
                <a href="{{ url('/login') }}" class="border border-white text-white px-3 py-1 rounded hover:bg-white hover:text-[#003942] transition">
                    Iniciar Sesión
                </a>

                <a href="{{ url('/register') }}" class="border border-white px-3 py-1 rounded hover:bg-white hover:text-[#003942] transition">
                    Registro
                </a>
            @endguest

            @auth
                <div class="relative group">

                    <button class="flex items-center space-x-2">

                        <span>{{ auth()->user()->name }}</span>

                        <img
                            src="{{ auth()->user()->avatar 
                                ? asset('storage/' . auth()->user()->avatar) 
                                : asset('img/user.png') }}"
                            class="w-8 h-8 rounded-full object-cover border border-white/30"
                            alt="Avatar"
                        >

                    </button>

                    <div class="absolute right-0 mt-2 w-40 bg-white text-[#003942] rounded shadow-lg hidden group-hover:block">

                        <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">
                            Perfil
                        </a>

                        <form method="POST" action="/logout">
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