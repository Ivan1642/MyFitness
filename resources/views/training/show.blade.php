@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">

        @if($session->photo)
            <img src="{{ asset('storage/' . $session->photo) }}"
                alt="Foto del entrenamiento"
                class="w-full h-56 object-cover">
        @endif

        <div class="bg-[#003942] px-6 py-4">
            <h1 class="text-white text-xl font-bold">
                Entrenamiento del {{ $session->date->format('d/m/Y') }}
            </h1>
            <p class="text-white/60 text-sm">{{ $session->date->format('H:i') }}</p>
        </div>

        <div class="p-6">

            <!-- Estadísticas -->
            <div class="grid grid-cols-3 gap-4 mb-6">

                <div class="text-center">
                    <p class="text-2xl font-bold text-[#003942]">
                        {{ $session->duration ? $session->duration . ' min' : '—' }}
                    </p>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Duración</p>
                </div>

                <div class="text-center border-x">
                    <p class="text-2xl font-bold text-[#003942]">
                        {{ number_format($totalVolume, 0) }}
                    </p>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Kg totales</p>
                </div>

                <div class="text-center">
                    <p class="text-2xl font-bold text-[#003942]">
                        {{ $exerciseGroups->count() }}
                    </p>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Ejercicios</p>
                </div>

            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center bg-gray-50 rounded-xl p-3">
                    <p class="text-xl font-bold text-[#003942]">{{ $totalSets }}</p>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Series totales</p>
                </div>
                <div class="text-center bg-gray-50 rounded-xl p-3">
                    <p class="text-xl font-bold text-[#003942]">{{ $totalReps }}</p>
                    <p class="text-gray-400 text-xs uppercase font-semibold">Reps totales</p>
                </div>
            </div>

            <!-- Notas -->
            @if($session->notes)
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-xs uppercase font-semibold text-gray-400 mb-1">Notas</p>
                    <p class="text-gray-700 text-sm">{{ $session->notes }}</p>
                </div>
            @endif

            <!-- Visibilidad -->
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                <span class="material-symbols-outlined text-base">
                    {{ $session->is_public ? 'public' : 'lock' }}
                </span>
                <span>{{ $session->is_public ? 'Pública' : 'Privada' }}</span>
            </div>

        </div>
    </div>

    <!-- Ejercicios -->
    <div class="space-y-4">
        @foreach($exerciseGroups as $exerciseId => $sets)
            @php $exercise = $sets->first()->exercise; @endphp
            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="bg-[#003942] px-4 py-3 flex items-center gap-3">
                    @if($exercise->image)
                        <img src="{{ $exercise->image }}" alt="{{ $exercise->name }}"
                            class="h-10 w-10 rounded-lg object-cover bg-white/10">
                    @else
                        <span class="material-symbols-outlined text-white/60">fitness_center</span>
                    @endif
                    <div>
                        <h3 class="font-bold text-white text-base">{{ $exercise->name }}</h3>
                        <p class="text-white/50 text-xs">{{ $exercise->muscle_group }}</p>
                    </div>
                </div>

                <div class="p-4">

                    <div class="grid grid-cols-3 gap-2 text-xs text-gray-400 font-semibold uppercase mb-2 px-1">
                        <span class="text-center">Serie</span>
                        <span class="text-center">Reps</span>
                        <span class="text-center">Kg</span>
                    </div>

                    @foreach($sets as $i => $set)
                        <div class="grid grid-cols-3 gap-2 py-2 border-b last:border-0">
                            <span class="text-center font-bold text-[#003942]">{{ $i + 1 }}</span>
                            <span class="text-center">{{ $set->repetitions }}</span>
                            <span class="text-center">{{ $set->weight }} kg</span>
                        </div>
                    @endforeach

                    <div class="mt-3 pt-2 flex justify-between text-xs text-gray-400">
                        <span>Volumen: <strong class="text-[#003942]">{{ number_format($sets->sum(fn($s) => $s->weight * $s->repetitions), 0) }} kg</strong></span>
                        <span>Max: <strong class="text-[#003942]">{{ $sets->max('weight') }} kg</strong></span>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    <!-- Volver -->
    <a href="{{ route('dashboard') }}"
        class="block text-center border-2 border-[#003942] text-[#003942] hover:bg-[#003942] hover:text-white py-3 rounded-xl font-semibold transition">
        Volver al dashboard
    </a>

</div>
@endsection