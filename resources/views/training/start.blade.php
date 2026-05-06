@extends('layouts.app')

@push('scripts')
    @vite('resources/js/pages/training.js')
@endpush

@section('content')
<div class="max-w-lg mx-auto">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Entrenamiento</h1>
        <p class="text-gray-400 text-sm">{{ now()->format('d/m/Y') }}</p>
    </div>

    <div id="training-app"></div>

</div>
@endsection