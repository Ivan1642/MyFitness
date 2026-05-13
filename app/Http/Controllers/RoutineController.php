<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use App\Models\RoutineSet;
use App\Models\TrainingSession;
use App\Models\Set;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = auth()->user()->routines()->with(['routineSets.exercise'])->get();
        return view('routines.index', compact('routines'));
    }

    public function create()
    {
        if (auth()->user()->routines()->count() >= 4) {
            return redirect()->route('routines.index')->with('error', 'Has alcanzado el límite de 4 rutinas.');
        }

        $exercises = \App\Models\Exercise::select('id', 'name', 'muscle_group')->orderBy('name')->get();
        return view('routines.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->routines()->count() >= 4) {
            return redirect()->route('routines.index')->with('error', 'Has alcanzado el límite de 4 rutinas.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'exercises'   => 'required|array|min:1',
        ]);

        $routine = Routine::create([
            'user_id'     => auth()->id(),
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        foreach ($request->exercises as $order => $ex) {
            if (empty($ex['exercise_id'])) continue;
            if (empty($ex['sets'])) continue;
            foreach ($ex['sets'] as $setOrder => $set) {
                if (empty($set['repetitions']) || !isset($set['weight'])) continue;
                RoutineSet::create([
                    'routine_id'  => $routine->id,
                    'exercise_id' => $ex['exercise_id'],
                    'set_order'   => $setOrder + 1,
                    'repetitions' => $set['repetitions'],
                    'weight'      => $set['weight'],
                ]);
            }
        }

        return redirect()->route('routines.index')->with('success', 'Rutina creada correctamente.');
    }

    public function edit($id)
    {
        $routine = Routine::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['routineSets.exercise'])
            ->firstOrFail();

        $exercises = \App\Models\Exercise::select('id', 'name', 'muscle_group')->orderBy('name')->get();

        $routineData = $routine->routineSets->groupBy('exercise_id')->map(function($sets) {
            return [
                'id'     => (string) $sets->first()->exercise_id,
                'name'   => $sets->first()->exercise->name,
                'muscle' => $sets->first()->exercise->muscle_group,
                'sets'   => $sets->map(fn($s) => ['reps' => $s->repetitions, 'weight' => $s->weight])->values()
            ];
        })->values();

        return view('routines.edit', compact('routine', 'exercises', 'routineData'));
    }

    public function update(Request $request, $id)
    {
        $routine = Routine::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'exercises'   => 'required|array|min:1',
        ]);

        $routine->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        $routine->routineSets()->delete();

        foreach ($request->exercises as $order => $ex) {
            if (empty($ex['exercise_id'])) continue;
            if (empty($ex['sets'])) continue;
            foreach ($ex['sets'] as $setOrder => $set) {
                if (empty($set['repetitions']) || !isset($set['weight'])) continue;
                RoutineSet::create([
                    'routine_id'  => $routine->id,
                    'exercise_id' => $ex['exercise_id'],
                    'set_order'   => $setOrder + 1,
                    'repetitions' => $set['repetitions'],
                    'weight'      => $set['weight'],
                ]);
            }
        }

        return redirect()->route('routines.index')->with('success', 'Rutina actualizada correctamente.');
    }

    public function destroy($id)
    {
        $routine = Routine::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $routine->delete();

        return redirect()->route('routines.index')->with('success', 'Rutina eliminada correctamente.');
    }

    public function start($id)
    {
        $routine = Routine::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['routineSets.exercise'])
            ->firstOrFail();

        TrainingSession::where('user_id', auth()->id())
            ->where('is_finished', false)
            ->whereDoesntHave('sets')
            ->delete();

        $session = TrainingSession::create([
            'user_id'     => auth()->id(),
            'routine_id'  => $routine->id,
            'date'        => now(),
            'is_finished' => false,
            'is_public'   => true,
        ]);

        $routineData = $routine->routineSets->groupBy('exercise_id')->map(function($sets) {
            return [
                'exercise_id'  => (string) $sets->first()->exercise_id,
                'name'         => $sets->first()->exercise->name,
                'image'        => $sets->first()->exercise->image,
                'muscle_group' => $sets->first()->exercise->muscle_group,
                'sets'         => $sets->map(fn($s) => [
                    'reps'   => $s->repetitions,
                    'weight' => $s->weight,
                    'saved'  => false
                ])->values()
            ];
        })->values();

        return redirect()->route('training.start')
            ->with('session_id', $session->id)
            ->with('routine_data', $routineData->toJson());
    }
}