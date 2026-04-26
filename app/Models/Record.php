<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $table = 'records';

    protected $fillable = [
        'user_id',
        'exercise_id',
        'max_weight',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}