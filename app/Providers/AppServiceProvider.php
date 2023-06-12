<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Follower;
use App\Observers\CommentObserver;
use App\Observers\FollowerObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Comment::observe(CommentObserver::class);
        Follower::observe(FollowerObserver::class);
    }
}
