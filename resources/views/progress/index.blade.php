@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold">Progreso</h1>

    <!-- Filtro grupo muscular -->
    <div class="bg-white rounded-2xl shadow p-4">
        <form method="GET" action="{{ route('progress.index') }}">
            <select name="muscle_group" onchange="this.form.submit()"
                class="w-full border-2 border-gray-200 rounded-xl p-3 text-[#003942] font-semibold focus:border-[#003942] focus:outline-none transition">
                <option value="todos" {{ $muscleGroup === 'todos' ? 'selected' : '' }}>
                    Todos los grupos musculares
                </option>
                @foreach($muscleGroups as $group)
                    <option value="{{ $group }}" {{ $muscleGroup === $group ? 'selected' : '' }}>
                        {{ $group }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Gráfica de volumen -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-[#003942] mb-6">Volumen por semana (kg)</h2>

        @if(collect($weeks)->sum('volume') > 0)
            @php $maxVolume = collect($weeks)->max('volume'); @endphp
            <div class="flex items-end gap-2 h-48">
                @foreach($weeks as $week)
                    @php
                        $height = $maxVolume > 0 ? round(($week['volume'] / $maxVolume) * 100) : 0;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        @if($week['volume'] > 0)
                            <span class="text-xs text-[#003942] font-bold">
                                {{ number_format($week['volume'], 0) }}
                            </span>
                        @endif
                        <div class="w-full rounded-t-lg bg-[#003942] transition-all"
                            style="height: {{ $height }}%">
                        </div>
                        <span class="text-xs text-gray-400">{{ $week['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-10">
                <span class="material-symbols-outlined text-5xl mb-3 block">monitoring</span>
                <p class="font-medium">No hay datos todavía</p>
                <p class="text-sm mt-1">Completa entrenamientos para ver tu progreso</p>
            </div>
        @endif
    </div>

    <!-- Records personales -->
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-[#003942] mb-4">Records personales</h2>

        @if($records->count())
            <div class="space-y-3">
                @foreach($records as $record)
                    <div class="flex justify-between items-center border-b pb-2 last:border-0">
                        <div>
                            <p class="font-semibold text-[#003942]">{{ $record->exercise->name }}</p>
                            <p class="text-xs text-gray-400">{{ $record->exercise->muscle_group }}</p>
                        </div>
                        <span class="font-bold text-[#003942] text-lg">{{ $record->max_weight }} kg</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-6">
                <span class="material-symbols-outlined text-4xl mb-2 block">emoji_events</span>
                <p class="font-medium">No hay records todavía</p>
            </div>
        @endif
    </div>

</div>
@endsection