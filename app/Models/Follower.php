<?php

namespace App\Models;

use App\Notifications\NewFollowerRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{

    protected $guarded = [];
    use HasFactory;


    public static function booted()
    {
        static::created(function ($follower) {
            $followTo = User::find($follower->follow_to);

            $followTo->notify(new NewFollowerRequest(auth()->user()));
        });

        // static::updated(function($follower){

        // });
    }
}
