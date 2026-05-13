@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Entrenamiento</h1>
        <p class="text-gray-400 text-sm">{{ now()->format('d/m/Y') }}</p>
    </div>

    <div id="training-app"></div>

</div>
@endsection

@push('scripts')
    <script>
        window.APP_URL = "{{ url('/') }}";
        @if(session('session_id'))
            window.PRELOADED_SESSION_ID = {{ session('session_id') }};
        @endif
        @if(session('routine_data'))
            window.PRELOADED_ROUTINE = {!! session('routine_data') !!};
        @endif
    </script>
    @vite('resources/js/pages/training.js')
@endpush