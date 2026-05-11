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
            'user_id'     => auth()->id(),
            'date'        => now(),
            'routine_id'  => null,
            'duration'    => null,
            'notes'       => null,
            'is_finished' => false,
            'is_public'   => true,
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

    public function finish(Request $request, $id)
    {
        $session = TrainingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'notes'    => 'nullable|string|max:500',
            'duration' => 'nullable|integer|min:1',
            'photo'    => 'nullable|image|max:4096',
        ]);

        $data = [
            'notes'       => $request->notes,
            'duration'    => $request->duration ?: null,
            'is_finished' => true,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('training_photos', 'public');
        }

        $session->update($data);

        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        $session = TrainingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $session->delete();

        return redirect()->route('dashboard')->with('success', 'Sesión eliminada correctamente.');
    }

    public function toggleVisibility(Request $request, $id)
    {
        $session = TrainingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $session->update(['is_public' => !$session->is_public]);

        return response()->json(['is_public' => $session->is_public]);
    }
}