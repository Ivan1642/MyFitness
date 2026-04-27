@extends('layouts.app')

@section('content')

<div class="flex flex-col items-center py-20 bg-white rounded shadow">

    <a href="{{ url('/') }}" class="mb-4">
        <img src="{{ asset('img/logoMyFitness2.png') }}" class="h-60 w-auto" alt="MyFitness">
    </a>

    <p class="text-[#003942] text-lg text-center mb-10 max-w-xl mx-auto opacity-80">
        Registra tus entrenamientos, supera tus marcas personales y sigue tu progreso como nunca antes.
    </p>

    <div class="space-x-4">
        <a href="{{ url('/register') }}" class="bg-[#003942] text-white px-6 py-3 rounded">
            Empieza gratis
        </a>

        <a href="{{ url('/login') }}" class="border border-[#003942] text-[#003942] px-6 py-3 rounded">
            Iniciar sesión
        </a>
    </div>

</div>

<div class="grid md:grid-cols-3 gap-6 mt-12">

    <div class="bg-white p-6 rounded shadow text-center">
        <h2 class="font-knewave text-xl mb-2 text-[#003942]">Progreso real</h2>
        <p class="text-[#003942] opacity-70">
            Controla tus marcas y evolución en el gimnasio.
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
        <h2 class="font-knewave text-xl mb-2 text-[#003942]">Entrenamientos</h2>
        <p class="text-[#003942] opacity-70">
            Crea rutinas y registra cada serie fácilmente.
        </p>
    </div>

    <div class="bg-white p-6 rounded shadow text-center">
        <h2 class="font-knewave text-xl mb-2 text-[#003942]">Comunidad</h2>
        <p class="text-[#003942] opacity-70">
            Sigue a otros usuarios y comparte tus logros.
        </p>
    </div>

</div>

<div class="text-center mt-16 py-12 bg-[#003942] text-white rounded">

    <h2 class="text-3xl font-knewave mb-4">
        ¿Listo para mejorar tu físico?
    </h2>

    <p class="mb-6 text-white/80">
        Empieza a entrenar de forma inteligente hoy mismo.
    </p>

    <a href="/register" class="bg-white text-[#003942] px-6 py-3 rounded font-bold">
        Crear cuenta
    </a>

</div>

@endsection