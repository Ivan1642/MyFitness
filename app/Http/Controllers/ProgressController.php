<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Set;
use App\Models\Record;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $muscleGroup = $request->get('muscle_group', 'todos');

        $muscleGroups = Set::whereHas('trainingSession', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('is_finished', true);
            })
            ->with('exercise')
            ->get()
            ->pluck('exercise.muscle_group')
            ->unique()
            ->sort()
            ->values();

        $weeks = [];
        for ($i = 7; $i >= 0; $i--) {
            $start = Carbon::now()->startOfWeek()->subWeeks($i);
            $end = Carbon::now()->startOfWeek()->subWeeks($i)->endOfWeek();

            $query = Set::whereHas('trainingSession', function($q) use ($user, $start, $end) {
                $q->where('user_id', $user->id)
                  ->where('is_finished', true)
                  ->whereBetween('date', [$start, $end]);
            });

            if ($muscleGroup !== 'todos') {
                $query->whereHas('exercise', function($q) use ($muscleGroup) {
                    $q->where('muscle_group', $muscleGroup);
                });
            }

            $volume = 0;
            foreach ($query->get() as $set) {
                $volume += $set->weight * $set->repetitions;
            }

            $weeks[] = [
                'label'  => $start->format('d/m'),
                'volume' => round($volume),
            ];
        }

        $recordsQuery = Record::where('user_id', $user->id)->with('exercise');

        if ($muscleGroup !== 'todos') {
            $recordsQuery->whereHas('exercise', function($q) use ($muscleGroup) {
                $q->where('muscle_group', $muscleGroup);
            });
        }

        $records = $recordsQuery->orderByDesc('max_weight')->get();

        return view('progress.index', compact('weeks', 'records', 'muscleGroups', 'muscleGroup'));
    }
}