@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Mis rutinas</h1>
        @if($routines->count() < 4)
            <a href="{{ route('routines.create') }}"
                class="bg-[#003942] text-white px-4 py-2 rounded-xl font-semibold hover:bg-[#002a31] transition">
                + Nueva rutina
            </a>
        @else
            <span class="text-sm text-gray-400">Límite de 4 rutinas alcanzado</span>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($routines->count())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($routines as $routine)
                <div class="bg-white rounded-2xl shadow overflow-hidden">

                    <div class="bg-[#003942] px-4 py-3 flex justify-between items-center">
                        <div>
                            <h2 class="font-bold text-white text-base">{{ $routine->name }}</h2>
                            <p class="text-white/50 text-xs">
                                {{ $routine->routineSets->groupBy('exercise_id')->count() }} ejercicios ·
                                {{ $routine->routineSets->count() }} series
                            </p>
                        </div>
                    </div>

                    <div class="p-4 space-y-2">

                        @if($routine->description)
                            <p class="text-gray-500 text-sm mb-3">{{ $routine->description }}</p>
                        @endif

                        @foreach($routine->routineSets->groupBy('exercise_id') as $exerciseId => $sets)
                            <div class="flex justify-between text-sm">
                                <span class="font-medium text-[#003942]">{{ $sets->first()->exercise->name }}</span>
                                <span class="text-gray-400">{{ $sets->count() }} series</span>
                            </div>
                        @endforeach

                    </div>

                    <div class="px-4 pb-4 flex gap-2">
                        <form method="POST" action="{{ route('routines.start', $routine->id) }}"
                            class="flex-1 routine-start-form">
                            @csrf
                            <button type="submit"
                                class="w-full bg-[#003942] text-white py-2 rounded-xl font-semibold hover:bg-[#002a31] transition text-sm">
                                Usar rutina
                            </button>
                        </form>
                        <a href="{{ route('routines.edit', $routine->id) }}"
                            class="border-2 border-[#003942] text-[#003942] px-4 py-2 rounded-xl font-semibold hover:bg-[#003942] hover:text-white transition text-sm">
                            Editar
                        </a>
                        <form method="POST" action="{{ route('routines.destroy', $routine->id) }}"
                            onsubmit="return confirm('¿Eliminar esta rutina?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="border-2 border-red-200 text-red-400 px-4 py-2 rounded-xl hover:bg-red-50 transition text-sm">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow p-12 text-center text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-3 block">fitness_center</span>
            <p class="font-medium">No tienes rutinas todavía</p>
            <a href="{{ route('routines.create') }}"
                class="inline-block mt-4 bg-[#003942] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[#002a31] transition text-sm">
                Crear primera rutina
            </a>
        </div>
    @endif

</div>
@endsection
@push('scripts')
<script>
    document.querySelectorAll('.routine-start-form').forEach(form => {
        form.addEventListener('submit', (e) => {
            const raw = localStorage.getItem('myfitness_training');
            if (raw) {
                const state = JSON.parse(raw);
                if (state.sessionId) {
                    if (!confirm('Tienes un entrenamiento en curso. ¿Quieres cancelarlo e iniciar uno nuevo con esta rutina?')) {
                        e.preventDefault();
                        return;
                    }
                    localStorage.removeItem('myfitness_training');
                }
            }
        });
    });
</script>
@endpush