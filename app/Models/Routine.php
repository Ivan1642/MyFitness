<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $table = 'routines';

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trainingSessions()
    {
        return $this->hasMany(TrainingSession::class, 'routine_id');
    }

    public function exercises()
    {
        return $this->belongsToMany(
            Exercise::class,
            'routine_exercise',
            'routine_id',
            'exercise_id'
        )->withPivot('order');
    }

    public function likes()
    {
        return $this->hasMany(RoutineLike::class, 'routine_id');
    }
}