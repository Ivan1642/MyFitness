@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Panel de administración</h1>
        <span class="text-sm text-gray-400">{{ $users->total() }} usuarios registrados</span>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow overflow-hidden">

        <div class="bg-[#003942] px-6 py-4">
            <h2 class="text-white font-bold">Usuarios</h2>
        </div>

        <div class="divide-y">
            @forelse($users as $user)
                <div class="flex items-center justify-between px-6 py-4 gap-4">

                    <div class="flex items-center gap-3">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('img/predeterminada_perfil.png') }}"
                            class="h-10 w-10 rounded-full object-cover">
                        <div>
                            <p class="font-semibold text-[#003942]">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $user->username ? '@'.$user->username : 'sin username' }} · {{ $user->email }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $user->training_sessions_count }} sesiones ·
                                {{ $user->followers_count }} seguidores ·
                                {{ $user->following_count }} siguiendo
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">

                        @if($user->isBanned())
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-lg font-semibold">
                                Baneado hasta {{ $user->banned_at->addDays(2)->format('d/m H:i') }}
                            </span>
                            <form method="POST" action="{{ route('admin.unban', $user->id) }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm border-2 border-[#003942] text-[#003942] px-3 py-1 rounded-xl hover:bg-[#003942] hover:text-white transition">
                                    Desbanear
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.ban', $user->id) }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm border-2 border-orange-300 text-orange-500 px-3 py-1 rounded-xl hover:bg-orange-500 hover:text-white transition">
                                    Banear 2 días
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.destroy', $user->id) }}"
                            onsubmit="return confirm('¿Eliminar a {{ $user->name }}? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-sm border-2 border-red-200 text-red-400 px-3 py-1 rounded-xl hover:bg-red-500 hover:text-white transition">
                                Eliminar
                            </button>
                        </form>

                    </div>

                </div>
            @empty
                <p class="text-gray-400 text-center py-10">No hay usuarios registrados</p>
            @endforelse
        </div>

        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>

    </div>

</div>
@endsection