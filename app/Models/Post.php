<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['is_liked'];

    public function scopeMyPosts(Builder $query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getIsLikedAttribute()
    {
        return $this->likes->contains('user_id', auth()->id()) ? 1 : 0;
    }
    public function followerUsers()
    {
        return $this->belongsToMany(
            User::class,
            'post_followers',
            'post_id',
            'user_id'
        )->withTimestamps();
    }
}
