@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('feed') }}" class="text-gray-400 hover:text-[#003942] transition">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold">Nueva publicación</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-2xl shadow p-6 space-y-4">

            <div class="flex items-center gap-3 mb-2">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('img/predeterminada_perfil.png') }}"
                    class="h-10 w-10 rounded-full object-cover">
                <p class="font-semibold text-[#003942]">{{ auth()->user()->name }}</p>
            </div>

            <div>
                <textarea name="content" rows="4"
                    class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition resize-none"
                    placeholder="¿Qué quieres compartir?">{{ old('content') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-[#003942] mb-1">Imagen (opcional)</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#003942] file:text-white hover:file:bg-[#002a31]">
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('feed') }}"
                    class="flex-1 text-center border-2 border-gray-200 text-gray-500 py-3 rounded-xl font-semibold hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="flex-1 bg-[#003942] text-white py-3 rounded-xl font-semibold hover:bg-[#002a31] transition">
                    Publicar
                </button>
            </div>

        </div>

    </form>

</div>
@endsection