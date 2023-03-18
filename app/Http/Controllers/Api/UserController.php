<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\EditUserRequest;
use App\Http\Requests\Api\User\NewUserRequest;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiTrait;

    public function userInfo()
    {
        try {
            return response()->json([
                'error' => 0,
                'data' => new UserResource(auth()->user()),
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function store(NewUserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            $token = $user->createToken('myApp')->plainTextToken;

            return response()->json([
                'error' => 0,
                'data' => new UserResource($user),
                'token' => $token
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->validated())) {
                $user = Auth::user();
                $token =  $user->createToken('myApp')->plainTextToken;

                return response()->json([
                    'error' => 0,
                    'data' => new UserResource($user),
                    'token' => $token
                ], Response::HTTP_CREATED);
            } else {
                return response()->json([
                    'error' => 1,
                    'message' => 'Email veya şifre hatalı'
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }


    public function update(EditUserRequest $request)
    {

        $hasPasspword =  $request->has('password');
        try {
            if ($hasPasspword) {
                auth()->user()->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'password' => Hash::make($request->get('name')),
                ]);
            } else {
                auth()->user()->update($request->validated());
            }

            $updatedUser = User::find(auth()->id());

            $responseData = [
                'error' => 0,
                'data' => new UserResource($updatedUser)
            ];
            if ($hasPasspword) {
                $responseData['passwordMessage'] = 'Your Password was updated successfully';
            }

            return response()->json($responseData, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::find(auth()->id());
            $user->delete();

            return response()->json([
                'error' => 0,
                'message' => 'User was deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {

            return $this->exceptionResponse($exception);
        }
    }
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'error' => 0,
            'message' => 'User was  logged out successfully'
        ], Response::HTTP_OK);
    }
}
