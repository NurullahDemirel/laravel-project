<?php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comment\StoreCommentRequest;
use App\Http\Resources\Api\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Like;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use ApiTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $comments = Comment::myComments()->with(['post', 'likes'])->get();

            return $this->apiSuccessResponse(['comments' => CommentResource::collection($comments)]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request)
    {
        try {
            $comment = auth()->user()->comments()->create($request->validated())->with('post.followerUsers');

            return $this->apiSuccessResponse(['comment' => new CommentResource($comment)], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        try {
            return $this->apiSuccessResponse(['comment' => new CommentResource($comment)]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        try {
            $comment->update($request->validated());

            return $this->apiSuccessResponse(['comment' => $comment]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        try {

            //delete likes for related with the comment
            //cascade on delete dont work with polimorpich relation ships
            //we have to delete like this for provading the DB normalization

            DB::transaction(function () use ($comment) {
                Like::getByType(Comment::class)->getByLikeableId($comment->id)->delete();
                $comment->delete();
            });

            return $this->apiSuccessResponse(null, Response::HTTP_OK, 'Successfully deleted');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
