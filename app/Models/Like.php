<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $guarded = [];

    //this consts data used for route structer
    public const LIKEABLE_TYPE_COMMENT = 'comment';

    public const LIKEABLE_TYPE_POST = 'post';

    public const LIKEABLE_TYPES = [self::LIKEABLE_TYPE_COMMENT, self::LIKEABLE_TYPE_POST];

    protected $appends = ['is_liked'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function scopeGetByType(Builder $query, $type)
    {
        return $query->where('likeable_type', $type);
    }

    public function scopeGetByLikeableId(Builder $query, $id)
    {
        return $query->where('likeable_id', $id);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getIsLikedAttribute()
    {
        return $this->likes->contains('user_id', auth()->id()) ? 1 : 0;
    }
}
