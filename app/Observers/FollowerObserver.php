<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Follower;
use App\Notifications\NewFollowerRequest;

class FollowerObserver
{
    /**
     * Handle the Follower "created" event.
     */
    public function created(Follower $follower): void
    {
        $followTo = User::find($follower->follow_to);

        $followTo->notify(new NewFollowerRequest($follower->follow_by));
    }

    /**
     * Handle the Follower "updated" event.
     */
    public function updated(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "deleted" event.
     */
    public function deleted(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "restored" event.
     */
    public function restored(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "force deleted" event.
     */
    public function forceDeleted(Follower $follower): void
    {
        //
    }
}
