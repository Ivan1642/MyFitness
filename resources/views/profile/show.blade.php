@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Tarjeta de perfil -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <div class="bg-[#003942] h-24"></div>

        <div class="px-6 pb-6">
            <div class="flex justify-between items-end -mt-12 mb-4">

                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/predeterminada_perfil.png') }}"
                    class="h-24 w-24 rounded-full border-4 border-white object-cover shadow"
                    alt="Avatar">

                <!-- Botón seguir/dejar de seguir -->
                @if(auth()->id() !== $user->id)
                    <form method="POST" action="{{ route('profile.follow', $user->id) }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition
                            {{ $isFollowing ? 'border-2 border-[#003942] text-[#003942] hover:bg-[#003942] hover:text-white' : 'bg-[#003942] text-white hover:bg-[#002a31]' }}">
                            {{ $isFollowing ? 'Dejar de seguir' : 'Seguir' }}
                        </button>
                    </form>
                @endif

                <a href="{{ route('profile.feed', $user->id) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold border-2 border-[#003942] text-[#003942] hover:bg-[#003942] hover:text-white transition">
                    Ver publicaciones
                </a>

            </div>

            <!-- Info -->
            <h1 class="text-2xl font-bold text-[#003942]">{{ $user->name }}</h1>

            @if($user->username)
                <p class="text-gray-400 text-sm">@ {{ $user->username }}</p>
            @endif

            @if($user->bio)
                <p class="text-gray-500 text-sm mt-1">{{ $user->bio }}</p>
            @endif

            <div class="flex gap-6 mt-4 text-sm">
                @if($user->weight)
                    <div class="text-center">
                        <p class="font-bold text-[#003942] text-lg">{{ $user->weight }} kg</p>
                        <p class="text-gray-400">Peso</p>
                    </div>
                @endif
                @if($user->height)
                    <div class="text-center">
                        <p class="font-bold text-[#003942] text-lg">{{ $user->height }} cm</p>
                        <p class="text-gray-400">Altura</p>
                    </div>
                @endif
            </div>

            <!-- Seguidores / Siguiendo -->
            <div class="flex gap-6 mt-4 text-sm">
                <button onclick="openModal('followersModal')" class="text-center hover:opacity-70 transition">
                    <p class="font-bold text-[#003942] text-lg">{{ $followers->count() }}</p>
                    <p class="text-gray-400">Seguidores</p>
                </button>
                <button onclick="openModal('followingModal')" class="text-center hover:opacity-70 transition">
                    <p class="font-bold text-[#003942] text-lg">{{ $following->count() }}</p>
                    <p class="text-gray-400">Siguiendo</p>
                </button>
            </div>

        </div>
    </div>

    <!-- Logros -->
    @if($achievements->count())
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-lg font-bold text-[#003942] mb-4">Logros conseguidos</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($achievements as $achievement)
                    <div class="bg-[#003942]/5 border border-[#003942]/20 rounded-xl p-3 text-center">
                        <span class="material-symbols-outlined text-[#003942] text-3xl mb-1 block">emoji_events</span>
                        <p class="text-sm font-semibold text-[#003942]">{{ $achievement->name }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $achievement->created_at->format('d/m/Y') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Volver -->
    <a href="{{ route('feed') }}"
        class="block text-center border-2 border-[#003942] text-[#003942] hover:bg-[#003942] hover:text-white py-3 rounded-xl font-semibold transition">
        Volver al feed
    </a>

</div>

<!-- seguidores -->
<div id="followersModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#003942]">Seguidores</h2>
            <button onclick="closeModal('followersModal')" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="space-y-3 max-h-72 overflow-y-auto">
            @forelse($followers as $f)
                <a href="{{ route('profile.show', $f->follower->id) }}" class="flex items-center gap-3 hover:opacity-70 transition">
                    <img src="{{ $f->follower->avatar ? asset('storage/' . $f->follower->avatar) : asset('img/predeterminada_perfil.png') }}"
                        class="h-10 w-10 rounded-full object-cover">
                    <span class="font-medium text-[#003942]">{{ $f->follower->name }}</span>
                </a>
            @empty
                <p class="text-gray-400 text-sm text-center">Nadie sigue a este usuario</p>
            @endforelse
        </div>
    </div>
</div>

<!-- siguiendo -->
<div id="followingModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-[#003942]">Siguiendo</h2>
            <button onclick="closeModal('followingModal')" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="space-y-3 max-h-72 overflow-y-auto">
            @forelse($following as $f)
                <a href="{{ route('profile.show', $f->following->id) }}" class="flex items-center gap-3 hover:opacity-70 transition">
                    <img src="{{ $f->following->avatar ? asset('storage/' . $f->following->avatar) : asset('img/predeterminada_perfil.png') }}"
                        class="h-10 w-10 rounded-full object-cover">
                    <span class="font-medium text-[#003942]">{{ $f->following->name }}</span>
                </a>
            @empty
                <p class="text-gray-400 text-sm text-center">No sigue a nadie todavía</p>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal.id);
        });
    });
</script>
@endpush