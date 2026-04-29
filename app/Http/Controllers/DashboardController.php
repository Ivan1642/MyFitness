<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $lastSession = $user->trainingSessions()
            ->latest('date')
            ->first();

        $sessions = $user->trainingSessions()
            ->latest('date')
            ->take(10)
            ->get();

        return view('dashboard', compact('lastSession', 'sessions'));
    }
}