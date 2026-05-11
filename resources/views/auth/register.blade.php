@extends('layouts.app')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">

        <h1 class="text-2xl font-bold text-center text-[#003942] mb-6">
            Crear cuenta
        </h1>

        <p class="text-center text-gray-500 mb-6">
            Empieza a registrar tus entrenamientos y mejorar tu progreso
        </p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#003942]">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942]">Nombre de usuario</label>
                <div class="flex items-center mt-1 border rounded focus-within:ring-2 focus-within:ring-[#003942]">
                    <span class="px-3 text-gray-400 font-medium">@</span>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="w-full p-2 focus:outline-none rounded-r">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942]">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942]">Contraseña</label>
                <input type="password" name="password"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942]">Confirmar contraseña</label>
                <input type="password" name="password_confirmation"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <button type="submit"
                class="w-full bg-[#003942] text-white py-2 rounded hover:bg-[#002a31] transition">
                Crear cuenta
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿Ya tienes cuenta?
            <a href="{{ url('/login') }}" class="text-[#003942] font-semibold hover:underline">
                Inicia sesión
            </a>
        </p>

    </div>

</div>

@endsection