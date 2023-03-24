<?php

namespace App\Http\Requests\Api\Like;

use App\Enums\LikeActions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class LikeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !is_null(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => Rule::when($this->has('post_id'),'required|exists:posts,id'),
            'comment_id' => Rule::when($this->has('comment_id'),'required|exists:comments,id'),
            'action_type' => ['required',new Enum(LikeActions::class)]
        ];
    }
}
