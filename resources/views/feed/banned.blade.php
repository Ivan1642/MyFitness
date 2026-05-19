@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10">
    <div class="bg-white rounded-2xl shadow p-8 text-center">
        <span class="material-symbols-outlined text-5xl text-red-400 mb-4 block">block</span>
        <h1 class="text-xl font-bold text-[#003942] mb-2">Acceso restringido</h1>
        <p class="text-gray-500 text-sm mb-4">
            Has sido baneado del feed temporalmente.
        </p>
        <p class="text-gray-400 text-xs">
            Tu baneo expira el <strong>{{ $bannedUntil }}</strong>
        </p>
        <a href="{{ route('dashboard') }}"
            class="inline-block mt-6 bg-[#003942] text-white px-6 py-2 rounded-xl font-semibold hover:bg-[#002a31] transition text-sm">
            Volver al inicio
        </a>
    </div>
</div>
@endsection