<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetModel extends Model
{
    protected $table = 'sets';

    protected $fillable = [
        'training_session_id',
        'exercise_id',
        'repetitions',
        'weight',
    ];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}