<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\LoginWith;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiTrait;
use Hash;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use ApiTrait;


    public function loginWithGithubRedirect()
    {

        try {
            $url = Socialite::driver('github')->stateless()->redirect()->getTargetUrl();

            return $this->apiSuccessResponse(['redirect_url' => $url], Response::HTTP_OK, 'Redirect url get successfully');
        } catch (\Exception  $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function loginWithGithubCallback()
    {

        try {
            $user = Socialite::driver('github')->stateless()->user();
            $userName = $user->nickname;
            $usermail = $user->email;

            if ($user = User::whereEmail($usermail)->first()) {
                $token = $user->createToken('myApp')->plainTextToken;
            } else {
                $user = User::create([
                    'name' => $userName,
                    'auth_type' => LoginWith::Github->value,
                    'email' => $usermail,
                    'password' => Hash::make($usermail)
                ]);
                $token = $user->createToken('myApp')->plainTextToken;
            }

            return $this->apiSuccessResponse(['token' => $token], Response::HTTP_OK, 'Login was success');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function loginWithGoogleRedirect()
    {

        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            return $this->apiSuccessResponse(['redirect_url' => $url], Response::HTTP_OK, 'Redirect url get successfully');
        } catch (\Exception  $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function loginWithGoogleCallback()
    {

        try {
            $user = Socialite::driver('google')->stateless()->user();
            $userName = $user->name;
            $usermail = $user->email;
            $avatarUrl = $user->avatar;

            if ($user = User::whereEmail($usermail)->first()) {
                $token = $user->createToken('myApp')->plainTextToken;
            } else {
                $user = User::create([
                    'name' => $userName,
                    'auth_type' => LoginWith::Github->value,
                    'email' => $usermail,
                    'password' => Hash::make($usermail)
                ]);
                $token = $user->createToken('myApp')->plainTextToken;
            }

            $user->addMediaFromUrl($avatarUrl)->toMediaCollection('profile');

            return $this->apiSuccessResponse(['token' => $token], Response::HTTP_OK, 'Login was success');
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}
