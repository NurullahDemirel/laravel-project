<?php

namespace App\Http\Requests\Api\User;

use App\Traits\ApiTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUserRequest extends FormRequest
{
    use ApiTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        // asetas@gmail.com

        return [
            'name' => 'required',
            //ignore auth user
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'password' => [Rule::when($this->has('password'), 'required|min:5')],
            'repeat_password' => [Rule::when($this->has('password'), 'required|same:password')]
        ];
    }

    public function failedValidation(Validator $validator)
    {
        return $this->apiRequestError($validator);
    }
}
