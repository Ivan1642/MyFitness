@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('routines.index') }}" class="text-gray-400 hover:text-[#003942] transition">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold">Editar rutina</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('routines.update', $routine->id) }}" id="routine-form">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow p-6 space-y-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-[#003942] mb-1">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $routine->name) }}"
                    class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#003942] mb-1">Descripción (opcional)</label>
                <textarea name="description" rows="2"
                    class="w-full border-2 border-gray-200 focus:border-[#003942] rounded-xl p-3 focus:outline-none transition resize-none"
                    placeholder="Ej: Rutina de fuerza para tren superior...">{{ old('description', $routine->description) }}</textarea>
            </div>
        </div>

        <div id="exercises-container" class="space-y-4 mb-6"></div>

        <div class="bg-white rounded-2xl shadow p-4 mb-6">
            <select id="exercise-select"
                class="w-full border-2 border-gray-200 rounded-xl p-4 text-[#003942] font-semibold focus:border-[#003942] focus:outline-none transition">
                <option value="">Selecciona un ejercicio...</option>
                @foreach($exercises as $exercise)
                    <option value="{{ $exercise->id }}"
                        data-name="{{ $exercise->name }}"
                        data-muscle="{{ $exercise->muscle_group }}">
                        {{ $exercise->name }}
                    </option>
                @endforeach
            </select>
            <button type="button" id="add-exercise"
                class="w-full mt-3 border-2 border-dashed border-[#003942]/30 hover:border-[#003942] text-[#003942] py-3 rounded-xl font-semibold text-sm transition">
                + Añadir ejercicio
            </button>
        </div>

        <button type="submit"
            class="w-full bg-[#003942] text-white py-4 rounded-xl font-bold text-lg hover:bg-[#002a31] transition">
            Guardar cambios
        </button>

    </form>

</div>
@endsection

@push('scripts')
    <script>
        window.ROUTINE_DATA = {!! json_encode($routineData) !!};
    </script>
    @vite('resources/js/pages/routine-edit.js')
@endpush