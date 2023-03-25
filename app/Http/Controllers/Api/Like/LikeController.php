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

class LikeController extends Controller
{
    use ApiTrait;

    public function likeOrDislike(LikeRequest $request, $likeableType)
    {
        try {

            if (!in_array($likeableType, Like::LIKEABLE_TYPES)) {
                return $this->apiErrorResponse('This type not valid for liking.');
            }


            if (($likeableType == Like::LIKEABLE_TYPE_POST) && ($this->checkRequestKey('post_id', $request))) {

              if($request->get('action_type') == LikeActions::LIKE){
                $post = Post::find($request->get('post_id'));

                $like = $post->likes()->create([
                    'user_id' => auth()->id(),
                ]);
              }
              else{

                //todo delete like
              }

            }


            if (($likeableType == Like::LIKEABLE_TYPE_COMMENT) && ($this->checkRequestKey('comment_id', $request))) {
                if($request->get('action_type') == LikeActions::LIKE){

                $comment = Comment::find($request->get('comment_id'));

                $like = $comment->likes()->create([
                    'user_id' => auth()->id(),
                ]);
                }
                else{
                    //tod delete
                }
            }


            return $this->apiSuccessResponse(['message' => "Successfully ${likeableType} liked"]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
