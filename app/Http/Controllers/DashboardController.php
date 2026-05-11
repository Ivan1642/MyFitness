<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $sessionsThisWeek = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        $volumeThisMonth = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->with('sets')
            ->get()
            ->flatMap->sets
            ->sum(fn($set) => $set->weight * $set->repetitions);

        $volumeLastMonth = $user->trainingSessions()
            ->where('is_finished', true)
            ->whereBetween('date', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->with('sets')
            ->get()
            ->flatMap->sets
            ->sum(fn($set) => $set->weight * $set->repetitions);

        $volumeDiff = $volumeThisMonth - $volumeLastMonth;

        $totalPRs = $user->records()->count();

        $lastSession = $user->trainingSessions()
            ->where('is_finished', true)
            ->latest('date')
            ->first();

        $sessions = $user->trainingSessions()
            ->where('is_finished', true)
            ->latest('date')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'sessionsThisWeek',
            'volumeThisMonth',
            'volumeDiff',
            'totalPRs',
            'lastSession',
            'sessions'
        ));
    }
}