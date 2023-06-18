<?php

namespace App\Http\Requests\Api\User;

use App\Traits\ApiTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FollowRequest extends FormRequest
{
    use ApiTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! is_null(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'follow_id' => 'required|exists:users,id',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return $this->apiRequestError($validator);
    }
}
