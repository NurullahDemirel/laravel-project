<?php

namespace App\Http\Resources\Api\Post;

use Illuminate\Http\Request;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'descirption' => $this->description,
            'user' => new UserResource($this->user),
            'comments' =>PostResource::collection($this->comments)
        ];
    }
}
