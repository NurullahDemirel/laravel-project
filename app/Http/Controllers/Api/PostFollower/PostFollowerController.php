<?php

namespace App\Http\Controllers\Api\PostFollower;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PostFollower\PostFollowerRequest;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostFollowerController extends Controller
{
    use ApiTrait;

    /**
     * Handle the incoming request.
     */
    public function __invoke(PostFollowerRequest $request)
    {
        try {
            $postId = $request->get('post_id');

            $collectIFollowed = auth()->user()->postIFollow();
            $postFollowed = $collectIFollowed->where('post_id', $postId)->first();

            if ($postFollowed) {
                $postFollowed->delete();
            } else {
                $collectIFollowed->create(['post_id' => $postId]);
            }

            $action = is_null($postFollowed) ? 'followed' : 'unfollowed';
            $statu = is_null($postFollowed) ? Response::HTTP_CREATED : Response::HTTP_OK;

            return $this->apiSuccessResponse(['mesage' => "post was ${action}"], $statu);

        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
