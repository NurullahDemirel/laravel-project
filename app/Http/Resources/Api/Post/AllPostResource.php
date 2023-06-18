<?php

namespace App\Http\Resources\Api\Psot;

use App\Http\Resources\Api\Comment\CommentResource;
use App\Http\Resources\Api\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'descirption' => $this->description,
            'is_liked' => $this->resource->is_liked,
            'user' => new UserResource(auth()->user()),
            'comments' => CommentResource::collection($this->comments),
        ];
    }
}
