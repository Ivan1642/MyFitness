@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold">Resumen</h1>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Sesiones esta semana</h2>
            <p class="text-3xl font-bold text-[#003942]">{{ $sessionsThisWeek }}</p>
        </div>

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Volumen este mes</h2>
            <p class="text-3xl font-bold text-[#003942]">
                {{ number_format($volumeThisMonth, 0) }} kg
            </p>
            <span class="text-sm font-normal {{ $volumeDiff >= 0 ? 'text-green-500' : 'text-red-500' }}">
                ({{ $volumeDiff >= 0 ? '+' : '' }}{{ number_format($volumeDiff, 0) }} kg vs mes anterior)
            </span>
        </div>

        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="font-bold text-gray-500 text-sm uppercase mb-1">Records personales</h2>
            <p class="text-3xl font-bold text-[#003942]">{{ $totalPRs }}</p>
        </div>

    </div>

    <!-- Botón iniciar entrenamiento -->
    <div class="bg-white p-6 shadow rounded text-center">

        @if($lastSession)
            <p class="text-gray-500 mb-4">
                Último entrenamiento: {{ $lastSession->date->diffForHumans() }}
            </p>
        @endif

        <h2 class="text-xl font-bold mb-2">¿Listo para entrenar?</h2>
        <p class="text-gray-500 mb-4">Empieza tu sesión y registra tu progreso en tiempo real.</p>

        <a href="{{ route('training.start') }}"
            class="bg-[#003942] text-white px-6 py-3 rounded font-semibold border hover:bg-white hover:text-[#003942] transition">
            Iniciar entrenamiento
        </a>

    </div>

    <!-- Historial -->
    <div class="bg-white p-4 shadow rounded">

        <h2 class="text-xl font-bold mb-4">Historial de entrenamientos</h2>

        @if($sessions->count())
            <div class="space-y-3">
                @foreach($sessions as $session)
                    <div class="border-b pb-3 flex justify-between items-center gap-2">

                        <div class="flex-1">
                            <p class="font-semibold">
                                {{ $session->date->format('d/m/Y H:i') }}
                            </p>
                            @if($session->notes)
                                <p class="text-gray-500 text-sm">{{ $session->notes }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">

                            <select
                                data-session-id="{{ $session->id }}"
                                class="visibility-select text-sm border rounded px-2 py-1 text-[#003942] focus:outline-none focus:ring-1 focus:ring-[#003942]">
                                <option value="1" {{ $session->is_public ? 'selected' : '' }}>Pública</option>
                                <option value="0" {{ !$session->is_public ? 'selected' : '' }}>Privada</option>
                            </select>

                            <a href="#" class="text-sm text-[#003942] hover:underline">Ver</a>

                            <form method="POST" action="{{ route('training.destroy', $session->id) }}"
                                onsubmit="return confirm('¿Eliminar este entrenamiento? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-sm text-red-400 hover:text-red-600 transition">
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No hay entrenamientos todavía</p>
        @endif

    </div>

</div>
@endsection

@push('scripts')
    <script>
        window.APP_URL = "{{ url('/') }}";
        document.querySelectorAll('.visibility-select').forEach(select => {
            select.addEventListener('change', async (e) => {
                const id = e.target.dataset.sessionId;
                await fetch(`${window.APP_URL}/training/session/${id}/visibility`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            });
        });
    </script>
@endpush