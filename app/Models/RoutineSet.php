<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineSet extends Model
{
    protected $table = 'routine_sets';

    protected $fillable = [
        'routine_id',
        'exercise_id',
        'set_order',
        'repetitions',
        'weight',
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}