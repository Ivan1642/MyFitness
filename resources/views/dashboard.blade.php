@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold">Resumen</h1>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Sesiones esta semana</h2>
            <p class="text-3xl font-bold text-[#003942]">{{ $sessionsThisWeek }}</p>
        </div>

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Volumen este mes</h2>
            <p class="text-3xl font-bold text-[#003942]">
                {{ number_format($volumeThisMonth, 0) }} kg
            </p>
            <span class="text-sm font-normal {{ $volumeDiff >= 0 ? 'text-green-500' : 'text-red-500' }}">
                ({{ $volumeDiff >= 0 ? '+' : '' }}{{ number_format($volumeDiff, 0) }} kg vs mes anterior)
            </span>
        </div>

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Records personales</h2>
            <p class="text-3xl font-bold text-[#003942]">{{ $totalPRs }}</p>
        </div>

    </div>

    <!-- Botón iniciar entrenamiento -->
    <div class="bg-white p-6 shadow rounded text-center">

        @if($lastSession)
            <p class="text-gray-500 mb-4">
                Último entrenamiento: {{ $lastSession->date->diffForHumans() }}
            </p>
        @endif

        <h2 class="text-xl font-bold mb-2">¿Listo para entrenar?</h2>
        <p class="text-gray-500 mb-4">Empieza tu sesión y registra tu progreso en tiempo real.</p>

        <a href="{{ route('training.start') }}"
            class="bg-[#003942] text-white px-6 py-3 rounded font-semibold border hover:bg-white hover:text-[#003942] transition">
            Iniciar entrenamiento
        </a>

    </div>

    <!-- Historial -->
    <div class="bg-white p-4 shadow rounded">

        <h2 class="text-xl font-bold mb-4">Historial de entrenamientos</h2>

        @if($sessions->count())
            <div class="space-y-3">
                @foreach($sessions as $session)
                    <div class="border-b pb-2 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">
                                {{ $session->date->format('d/m/Y H:i') }}
                            </p>
                            @if($session->notes)
                                <p class="text-gray-500 text-sm">{{ $session->notes }}</p>
                            @endif
                        </div>
                        <a href="#" class="text-sm text-[#003942] hover:underline">Ver →</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No hay entrenamientos todavía</p>
        @endif

    </div>

</div>
@endsection