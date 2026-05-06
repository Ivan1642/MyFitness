<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\Set;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{
    public function start()
    {
        return view('training.start');
    }

    public function store(Request $request)
    {
        $session = TrainingSession::create([
            'user_id'    => auth()->id(),
            'date'       => now(),
            'routine_id' => null,
            'duration'   => null,
            'notes'      => null,
        ]);

        return response()->json(['session_id' => $session->id]);
    }

    public function storeSet(Request $request)
    {
        $request->validate([
            'session_id'  => 'required|exists:training_sessions,id',
            'exercise_id' => 'required|exists:exercises,id',
            'repetitions' => 'required|integer|min:1',
            'weight'      => 'required|numeric|min:0',
        ]);

        $set = Set::create([
            'training_session_id' => $request->session_id,
            'exercise_id'         => $request->exercise_id,
            'repetitions'         => $request->repetitions,
            'weight'              => $request->weight,
        ]);

        return response()->json(['ok' => true, 'set_id' => $set->id]);
    }
}