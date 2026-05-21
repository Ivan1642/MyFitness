@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <h1 class="text-2xl font-bold">Notificaciones</h1>

    @if($notifications->count())
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-2xl shadow p-4 flex items-start gap-4 {{ $notification->read ? 'opacity-60' : '' }}">

                    <div class="rounded-full p-2 flex-shrink-0
                        {{ $notification->type === 'pr' ? 'bg-yellow-100' : '' }}
                        {{ $notification->type === 'achievement' ? 'bg-purple-100' : '' }}
                        {{ $notification->type === 'like' ? 'bg-red-100' : '' }}
                        {{ $notification->type === 'follower' ? 'bg-blue-100' : '' }}">
                        <span class="material-symbols-outlined text-xl
                            {{ $notification->type === 'pr' ? 'text-yellow-500' : '' }}
                            {{ $notification->type === 'achievement' ? 'text-purple-500' : '' }}
                            {{ $notification->type === 'like' ? 'text-red-500' : '' }}
                            {{ $notification->type === 'follower' ? 'text-blue-500' : '' }}">
                            {{ $notification->type === 'pr' ? 'monitoring' : '' }}
                            {{ $notification->type === 'achievement' ? 'emoji_events' : '' }}
                            {{ $notification->type === 'like' ? 'favorite' : '' }}
                            {{ $notification->type === 'follower' ? 'person_add' : '' }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <p class="font-semibold text-[#003942] text-sm">{{ $notification->title }}</p>
                            @if(!$notification->read)
                                <span class="h-2 w-2 rounded-full bg-[#003942] mt-1 flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-gray-500 text-sm mt-1">{{ $notification->body }}</p>
                        <p class="text-xs text-gray-300 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow p-12 text-center text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-3 block">notifications</span>
            <p class="font-medium">No tienes notificaciones</p>
        </div>
    @endif

</div>
@endsection