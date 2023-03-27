<?php

namespace App\Models;

use App\Jobs\NewCommentJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function booted()
    {
        static::created(function ($comment) {
            $commentBy = auth()->user();
            $post = $comment->post;
            $followers = $post->followerUsers;
            NewCommentJob::dispatchSync($post,$commentBy,$followers);
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }


    public function scopeMyComments(Builder $query)
    {
        return $query->where('user_id', auth()->id());
    }
}
