@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">Resumen</h1>

    <!-- Resumen de los entrenamientos -->
    <div class="grid grid-cols-3 gap-4 mb-6">

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-bold">Entrenamientos</h2>
            <p>0 esta semana</p>
        </div>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-bold">Progreso</h2>
            <p>+0 kg este mes</p>
        </div>

        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-bold">PRs</h2>
            <p>0 récords</p>
        </div>

    </div>

    <div class="bg-white p-6 shadow rounded text-center">

        <!-- Ultimo entrenamiento -->
        @if($lastSession)
            <p class="text-black mb-4">
                Último entrenamiento: {{ $lastSession->date->diffForHumans() }}
            </p>
        @endif

        <!-- Iniciar entrenamiento libre -->
        <h2 class="text-xl mb-4 font-bold">¿Listo para entrenar?</h2>

        <p class="text-black mb-4">
            Empieza tu sesión y registra tu progreso en tiempo real.
        </p>

        <a href="{{ route('training.start') }}" 
        class="bg-[#003942] text-white px-6 py-3 rounded font-semibold border hover:bg-white hover:text-[#003942] transition">
            Iniciar entrenamiento libre
        </a>

    </div>

    <!-- Historial de entrenamientos -->
    <div class="bg-white mt-4 p-4 shadow rounded">

        <h2 class="text-xl font-bold mb-4">Historial de entrenamientos</h2>

        @if($sessions->count())

            <div class="space-y-3">

                @foreach($sessions as $session)
                    <div class="border-b pb-2 flex justify-between">

                        <div>
                            <p class="font-semibold">
                                Entrenamiento del {{ $session->date->format('d/m/Y H:i') }}
                            </p>

                            @if($session->notes)
                                <p class="text-black text-sm">
                                    {{ $session->notes }}
                                </p>
                            @endif
                        </div>

                        <span class="text-sm text-black">
                            Ver →
                        </span>

                    </div>
                @endforeach

            </div>

        @else
            <p class="text-gray-500">No hay entrenamientos todavía</p>
        @endif

    </div>

    </div>
</div>
@endsection