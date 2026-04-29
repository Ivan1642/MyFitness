@extends('layouts.app')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow-xl rounded-lg p-8">

        <h1 class="text-2xl font-bold text-center text-[#003942] mb-6">
            Iniciar sesión
        </h1>

        <p class="text-center text-gray-500 mb-6">
            Accede a tu cuenta y continúa tu progreso
        </p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ url('/login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[#003942]">
                    Email
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942]">
                    Contraseña
                </label>
                <input type="password" name="password"
                    class="w-full mt-1 p-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#003942]">
            </div>

            <div class="flex items-center justify-between text-sm">

                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember">
                    <span class="text-gray-600">Recordarme</span>
                </label>

                <a href="#" class="text-[#003942] hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>

            </div>

            <button type="submit"
                class="w-full bg-[#003942] text-white py-2 rounded hover:bg-[#002a31] transition">
                Entrar
            </button>

        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            ¿No tienes cuenta?
            <a href="{{ url('/register') }}" class="text-[#003942] font-semibold hover:underline">
                Crear cuenta
            </a>
        </p>

    </div>

</div>

@endsection