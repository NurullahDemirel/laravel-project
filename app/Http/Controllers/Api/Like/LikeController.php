<?php

namespace App\Http\Controllers\Api\Like;

use App\Enums\LikeActions;
use App\Models\Post;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Like\LikeRequest;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    use ApiTrait;

    public function likeOrDislike(LikeRequest $request, $likeableType)
    {
        try {

        if (!in_array($likeableType, Like::LIKEABLE_TYPES)) {
            return $this->returnWithMessag('This type not valid to like or dislike.',1,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (($likeableType == Like::LIKEABLE_TYPE_POST) && ($this->checkRequestKey('post_id', $request))) {
            $postId = $request->get('post_id');

            $post = Post::with('likes')->find($postId);

            $like = $post->likes->where('user_id', auth()->id())->first();

            if (!$like) {
                $post->likes()->create([
                    'user_id' => auth()->id(),
                ]);
            } else {
                $like->delete();
            }
        }


        if (($likeableType == Like::LIKEABLE_TYPE_COMMENT) && ($this->checkRequestKey('comment_id', $request))) {
            $commentId = $request->get('comment_id');



            $comment = Comment::with('likes')->find($commentId);

            $like = $comment->likes->where('user_id', auth()->user)->first();

            if (!$like) {
                $comment->likes()->create([
                    'user_id' => auth()->id(),
                ]);
            } else {
                auth()->user()->likes()->getByType(Comment::class)->getByLikeableId($commentId)->delete();
            }
        }



        $process =  is_null($like ) == LikeActions::LIKE->value ? 'liked' : 'disliked';

        return $this->apiSuccessResponse(['message' => "Successfully ${likeableType} ${process}"]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
