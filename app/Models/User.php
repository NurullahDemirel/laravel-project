<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia;

    const AWS_PROFILE_IMAGES_PATH = 'User/Profile/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function registerMediaCollections(): void // her kişi bir tane profil resmi olabilir
    {
        $this->addMediaCollection('profile')
            ->singleFile();
    }

    public function posts()//bir userın birden çok posu olabilir
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }


    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', 'id');
    }
    public function postIFollow()
    {
        return $this->belongsToMany(
            Post::class,
            'post_followers',
            'user_id',
            'post_id'
        )->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'follow_to', 'follow_by')
            ->wherePivot('is_accepted', 1);
    }

    public function sendingRequests()//ben kime istek attım ?
    {
        return $this->belongsToMany(User::class, 'followers', 'follow_by', 'follow_to');
    }

    public function followsByMe()//ben kimi takip ediyorum ?
    {
        return $this->sendingRequests()->wherePivot('is_accepted', 1);
    }

    public function pendingRequests()//ben kime istek attım da  kabul etmedi?
    {
        return $this->sendingRequests()->wherePivot('is_accepted', 0);
    }
}
