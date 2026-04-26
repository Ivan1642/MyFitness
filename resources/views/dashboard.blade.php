@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

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

    <!-- Entrenamiento activo -->
    <div class="bg-white p-4 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Entrenamiento actual</h2>

        <p>No hay sesión activa</p>

        <button class="mt-4 bg-black text-white px-4 py-2 rounded">
            Iniciar entrenamiento
        </button>
    </div>

</div>
@endsection