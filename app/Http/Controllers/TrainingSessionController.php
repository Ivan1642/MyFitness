<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{

    public function store(Request $request)
    {
        $session = TrainingSession::create([
            'user_id' => auth()->id(),
            'date' => now(),
            'routine_id' => null,
            'duration' => null,
            'notes' => null,
        ]);

        return response()->json([
            'session_id' => $session->id
        ]);
    }

    /* Show */
    public function start()
    {
        return view('training.start');
    }
}
