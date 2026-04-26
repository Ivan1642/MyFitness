<header class="bg-[#003942] text-white shadow-md">

    <div class="container mx-auto flex justify-between items-center p-2">

        <a href="/" class="flex items-center">
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
                <a href="/login" class="bg-white text-[#003942] px-3 py-1 rounded">
                    Login
                </a>

                <a href="/register" class="border border-white px-3 py-1 rounded">
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