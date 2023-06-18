<?php

namespace App\Http\Resources\Api\Post;

use App\Http\Resources\Api\Comment\CommentResource;
use Illuminate\Http\Request;
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
            'id' => $this->id,
            'title' => $this->title,
            'descirption' => $this->description,
            'is_liked' => $this->resource->is_liked,
            'comments' => CommentResource::collection($this->comments),
        ];
    }
}
