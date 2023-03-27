<?php

namespace App\Jobs;

use App\Mail\PostFollower;
use App\Models\Post;
use App\Models\User;
use App\Mail\VerifyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class NewCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post, public User $commentby, public $postFollowers)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emails = $this->postFollowers->pluck('email')->toArray();
        Mail::to($emails)->send(new PostFollower($this->post, $this->commentby));
    }
}
