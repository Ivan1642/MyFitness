<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function trainingSessions()
    {
        return $this->hasMany(TrainingSession::class, 'user_id');
    }

    public function routines()
    {
        return $this->hasMany(Routine::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'user_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class, 'user_id');
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'following_id');
    }

    public function following()
    {
        return $this->hasMany(Follower::class, 'follower_id');
    }
}