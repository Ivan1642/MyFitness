<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(SetModel::class, 'training_session_id');
    }
}