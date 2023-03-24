<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $guarded = [] ;

    public const LIKE = 'Like';

    public const DISLIKE = 'DisLike';

    public const LIKEABLE_TYPES = ['comment','post'];
    public const LIKEABLE_TYPE_COMMENT = 'comment';
    public const LIKEABLE_TYPE_POST = 'post';

    public function commentable()
    {
        return $this->morphTo();
    }
}
