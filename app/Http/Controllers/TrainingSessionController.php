<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\Set;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\Exercise;
use App\Services\NotificationService;
use App\Services\AchievementService;

class TrainingSessionController extends Controller
{

    public function show($id)
    {
        $session = TrainingSession::where('id', $id)
            ->where(function($q) {
                $q->where('user_id', auth()->id())
                ->orWhere('is_public', true);
            })
            ->with(['sets.exercise'])
            ->firstOrFail();

        $totalVolume = 0;
        foreach ($session->sets as $set) {
            $totalVolume += $set->weight * $set->repetitions;
        }

        $totalSets = $session->sets->count();
        $totalReps = $session->sets->sum('repetitions');
        $exerciseGroups = $session->sets->groupBy('exercise_id');

        return view('training.show', compact('session', 'totalVolume', 'totalSets', 'totalReps', 'exerciseGroups'));
    }
    
    public function start()
    {
        return view('training.start');
    }

    public function store(Request $request)
    {
        TrainingSession::where('user_id', auth()->id())
            ->where('is_finished', false)
            ->whereDoesntHave('sets')
            ->delete();

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

        $record = Record::where('user_id', auth()->id())
            ->where('exercise_id', $request->exercise_id)
            ->first();

        if (!$record) {
            Record::create([
                'user_id'     => auth()->id(),
                'exercise_id' => $request->exercise_id,
                'max_weight'  => $request->weight,
            ]);
            $exercise = Exercise::find($request->exercise_id);
            (new NotificationService())->newPR(auth()->user(), $exercise);
        } elseif ($request->weight > $record->max_weight) {
            $record->update(['max_weight' => $request->weight]);
            $exercise = Exercise::find($request->exercise_id);
            (new NotificationService())->newPR(auth()->user(), $exercise);
        }

        (new AchievementService())->check(auth()->user());

        return response()->json(['ok' => true, 'set_id' => $set->id]);
    }

    public function finish(Request $request, $id)
    {
        $session = TrainingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        \Log::info('Sesión encontrada', ['is_finished_antes' => $session->is_finished]);

        $request->validate([
            'notes'    => 'nullable|string|max:500',
            'duration' => 'nullable|integer|min:0',
            'photo'    => 'nullable|image|max:4096',
        ]);

        $data = [
            'notes'       => $request->notes,
            'duration'    => $request->duration !== '' ? (int) $request->duration : 0,
            'is_finished' => 1,
        ];

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('training_photos', 'public');
        }

        $result = $session->update($data);

        \Log::info('Update ejecutado', ['result' => $result, 'is_finished_despues' => $session->fresh()->is_finished]);

        (new AchievementService())->check(auth()->user());

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

    public function cancel($id)
    {
        TrainingSession::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['ok' => true]);
    }
}