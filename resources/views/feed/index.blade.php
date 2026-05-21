@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="flex gap-4">
        <!-- Mi feed -->
        <a href="{{ route('profile.feed', auth()->id()) }}"
            class="flex-1 flex items-center justify-center gap-2 bg-white rounded-2xl shadow px-4 py-3 text-[#003942] font-semibold text-sm hover:bg-[#003942] hover:text-white transition">
            <span class="material-symbols-outlined text-sm">grid_view</span>
            Mis publicaciones
        </a>
        <!-- Mi perfil -->
        <a href="{{ route('profile') }}"
            class="flex-1 flex items-center justify-center gap-2 bg-white rounded-2xl shadow px-4 py-3 text-[#003942] font-semibold text-sm hover:bg-[#003942] hover:text-white transition">
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('img/predeterminada_perfil.png') }}"
                class="h-6 w-6 rounded-full object-cover">
            Mi perfil
        </a>
        <!-- Crear posts -->
        <a href="{{ route('posts.create') }}"
            class="flex-1 flex items-center justify-center gap-2 bg-[#003942] rounded-2xl shadow px-4 py-3 text-white font-semibold text-sm hover:bg-[#002a31] transition">
            <span class="material-symbols-outlined text-sm">add</span>
            Publicar
        </a>
    </div>

    <!-- Busqueda -->
    <div class="bg-white rounded-2xl shadow p-4 relative">
        <input id="search-input" type="text" placeholder="Buscar usuarios..."
            class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition">
        <div id="search-results"
            class="absolute left-4 right-4 bg-white rounded-xl shadow-xl z-50 hidden mt-1">
        </div>
    </div>

    <!-- Selector de feed -->
    <div class="bg-white rounded-2xl shadow p-2 flex gap-2">
        <button id="btn-para-ti"
            class="flex-1 py-2 rounded-xl font-semibold text-sm transition feed-tab active-tab">
            Para ti
        </button>
        <button id="btn-seguidos"
            class="flex-1 py-2 rounded-xl font-semibold text-sm transition feed-tab">
            Seguidos
        </button>
    </div>

    <!-- Feed -->
    <div id="feed-container" class="space-y-4"></div>

    <!-- Contenedor simbolo de cargar -->
    <div id="feed-loading" class="text-center py-6 hidden">
        <span class="material-symbols-outlined text-3xl text-gray-300 animate-spin">refresh</span>
    </div>

    <!-- Si se acaban las publicaciones -->
    <div id="feed-end" class="text-center py-6 text-gray-400 text-sm hidden">
        No hay más publicaciones
    </div>

</div>
@endsection

@push('scripts')
    <script>
        window.APP_URL = "{{ url('/') }}";
        window.CSRF = "{{ csrf_token() }}";
        window.AUTH_USER_ID = {{ auth()->id() }};
    </script>
    @vite('resources/js/pages/feed.js')
@endpush