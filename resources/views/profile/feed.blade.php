@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="flex items-center gap-4">
        <a href="{{ route('profile.show', $user->id) }}" class="text-gray-400 hover:text-[#003942] transition">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div class="flex items-center gap-3">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/predeterminada_perfil.png') }}"
                class="h-10 w-10 rounded-full object-cover">
            <h1 class="text-xl font-bold text-[#003942]">{{ $user->name }}</h1>
        </div>
    </div>

    <!-- Feed -->
    <div id="feed-container" class="space-y-4"></div>

    <!-- simbolo de carga -->
    <div id="feed-loading" class="text-center py-6 hidden">
        <span class="material-symbols-outlined text-3xl text-gray-300 animate-spin">refresh</span>
    </div>

    <div id="feed-end" class="text-center py-6 text-gray-400 text-sm hidden">
        No hay más publicaciones
    </div>

</div>
@endsection

@push('scripts')
    <script>
        window.APP_URL = "{{ url('/') }}";
        window.CSRF = "{{ csrf_token() }}";
        window.PROFILE_ID = {{ $user->id }};
        window.AUTH_USER_ID = {{ auth()->id() }};
    </script>
    @vite('resources/js/pages/profile-feed.js')
@endpush