<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Routine;
use App\Models\Set;

class TrainingSession extends Model
{
    protected $table = 'training_sessions';

    protected $fillable = [
        'user_id',
        'routine_id',
        'date',
        'duration',
        'notes',
        'photo',
        'is_finished',
        'is_public',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function routine()
    {
        return $this->belongsTo(Routine::class, 'routine_id');
    }

    public function sets()
    {
        return $this->hasMany(Set::class, 'training_session_id');
    }
}