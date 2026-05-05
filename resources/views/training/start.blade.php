@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="bg-white p-6 shadow rounded">
        <h1 class="text-2xl font-bold mb-2">
            Entrenamiento en curso
        </h1>

        <p class="text-gray-500">
            {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <!-- App mount -->
    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-xl font-semibold mb-4">
            Ejercicios
        </h2>

        <div id="training-app"></div>
    </div>

    <!-- Acciones -->
    <div class="bg-white p-6 shadow rounded flex justify-between items-center">

        <button class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
            Cancelar
        </button>

        <button class="bg-black text-white px-6 py-2 rounded hover:bg-gray-800">
            Finalizar entrenamiento
        </button>

    </div>

</div>
@endsection