<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    // Added: Allow mass assignment of user_id and daily_goal
    protected $fillable = [
        'user_id',
        'daily_goal'
    ];
}
