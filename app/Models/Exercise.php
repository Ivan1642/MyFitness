<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $table = 'exercises';

    protected $fillable = [
        'name',
        'muscle_group',
        'description',
        'image',
    ];

    public function routines()
    {
        return $this->belongsToMany(
            Routine::class,
            'routine_exercise',
            'exercise_id',
            'routine_id'
        )->withPivot('order');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'exercise_id');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'exercise_id');
    }
}