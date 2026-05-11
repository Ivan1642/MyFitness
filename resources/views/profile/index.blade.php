@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="bg-[#003942] h-24"></div>

        <div class="px-6 pb-6">
            <div class="flex justify-between items-end -mt-12 mb-4">

                <div class="relative">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('img/predeterminada_perfil.png') }}"
                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow"
                        alt="Avatar">
                </div>

                <button id="editBtn"
                    class="bg-[#003942] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#002a31] transition">
                    Editar perfil
                </button>

            </div>

            <!-- Perfil modo vista -->
            <div id="viewMode">
                <h1 class="text-2xl font-bold text-[#003942]">{{ $user->name }}</h1>
                @if($user->username)
                    <p class="text-gray-400 text-sm">@ {{ $user->username }}</p>
                @endif

                @if($user->bio)
                    <p class="text-gray-500 text-sm mt-1">{{ $user->bio }}</p>
                @else
                    <p class="text-gray-300 text-sm mt-1 italic">Sin biografía</p>
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
                    <button onclick="openModal('followersModal')"
                        class="text-center hover:opacity-70 transition">
                        <p class="font-bold text-[#003942] text-lg">{{ $followers->count() }}</p>
                        <p class="text-gray-400">Seguidores</p>
                    </button>
                    <button onclick="openModal('followingModal')"
                        class="text-center hover:opacity-70 transition">
                        <p class="font-bold text-[#003942] text-lg">{{ $following->count() }}</p>
                        <p class="text-gray-400">Siguiendo</p>
                    </button>
                </div>
            </div>

            <!-- Formulario edición -->
            <form id="editMode" method="POST" action="{{ route('profile.update') }}"
                enctype="multipart/form-data" class="hidden space-y-4 mt-2">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-[#003942] mb-1">Foto de perfil</label>
                    <input type="file" name="avatar" accept="image/*"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#003942] file:text-white hover:file:bg-[#002a31]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#003942] mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#003942] mb-1">Nombre de usuario</label>
                    <div class="flex items-center border-2 border-gray-200 focus-within:border-[#003942] rounded-xl transition">
                        <span class="px-3 text-gray-400 font-medium">@</span>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                            class="w-full p-3 focus:outline-none rounded-r-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-[#003942] mb-1">Biografía</label>
                    <textarea name="bio" rows="3"
                        class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition resize-none"
                        placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[#003942] mb-1">Peso (kg)</label>
                        <input type="number" name="weight" value="{{ old('weight', $user->weight) }}"
                            step="0.1" min="0" max="300"
                            class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#003942] mb-1">Altura (cm)</label>
                        <input type="number" name="height" value="{{ old('height', $user->height) }}"
                            min="0" max="300"
                            class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition">
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-3 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="flex gap-3">
                    <button type="button" id="cancelBtn"
                        class="flex-1 border-2 border-gray-200 text-gray-500 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 bg-[#003942] text-white py-3 rounded-xl font-semibold hover:bg-[#002a31] transition">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>

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
                <div class="flex items-center gap-3">
                    <img src="{{ $f->follower->avatar ? asset('storage/' . $f->follower->avatar) : asset('img/predeterminada_perfil.png') }}"
                        class="h-10 w-10 rounded-full object-cover">
                    <span class="font-medium text-[#003942]">{{ $f->follower->name }}</span>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center">Nadie te sigue todavía</p>
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
                <div class="flex items-center gap-3">
                    <img src="{{ $f->following->avatar ? asset('storage/' . $f->following->avatar) : asset('img/predeterminada_perfil.png') }}"
                        class="h-10 w-10 rounded-full object-cover">
                    <span class="font-medium text-[#003942]">{{ $f->following->name }}</span>
                </div>
            @empty
                <p class="text-gray-400 text-sm text-center">No sigues a nadie todavía</p>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');

    editBtn.addEventListener('click', () => {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.classList.add('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
        editBtn.classList.remove('hidden');
    });

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

    @if($errors->any())
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.classList.add('hidden');
    @endif
</script>
@endpush