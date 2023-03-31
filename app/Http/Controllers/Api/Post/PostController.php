<?php

namespace App\Http\Controllers\Api\Post;

use App\Models\Like;
use App\Models\Post;
use App\Traits\ApiTrait;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Post\PostResource;
use App\Http\Requests\Api\Post\StorePostRequest;
use App\Http\Requests\Api\Post\UpdatePostRequest;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use ApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::myPosts()->with(['likes', 'comments'])->get();
        try {
            return $this->apiSuccessResponse(PostResource::collection($posts));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        try {
            $posts = auth()->user()->posts()->create($request->validated());

            return $this->apiSuccessResponse(new PostResource($posts), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($postId)
    {
        try {
            $post = Post::with(['comments.user', 'user'])->find($postId);

            if (!$post)
                return $this->returnWithMessag('Post not found',1, Response::HTTP_NOT_FOUND);

            return $this->apiSuccessResponse(new PostResource($post));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {

        try {
            $post->update($request->validated());

            $updatedPost = Post::find($post->id);

            return $this->apiSuccessResponse(new PostResource($post));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            //delete likes for related with the posts
            //cascade on delete dont work with polimorpich relation ships
            //we have to delete like this for provading the DB normalization

            DB::transaction(function () use ($post) {
                Like::getByType(Post::class)->getByLikeableId($post->id)->delete();
                $post->delete();
            });
            return $this->apiSuccessResponse(['message' => "Successfully deleted"]);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function allPosts()
    {

        try {
            $followers = auth()->user()->followers()->select('follow_by')->get()->pluck('follow_by')->toArray();

            $posts = Post::whereIn('user_id', $followers)->with('comments', 'user')->withCount('likes','comments')->get();
            return $this->apiSuccessResponse($posts);
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
