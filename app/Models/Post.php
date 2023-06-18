<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;

class Post extends Model implements ReactableInterface
{
    use HasFactory, Reactable;

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

    public function getIsLikedAttribute() //bu postu beğenenler arasında ben var mıyım?
    {
        return $this->likes->contains('user_id', auth()->id()) ? 1 : 0;
    }

    public function followerUsers() //beni hangi kullanıcılar takip ediyor ?
    {
        return $this->belongsToMany(
            User::class,
            'post_followers',
            'post_id',
            'user_id'
        )->withTimestamps();
    }
}
