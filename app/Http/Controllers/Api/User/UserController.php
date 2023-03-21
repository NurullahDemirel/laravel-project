<?php

namespace App\Http\Controllers\Api\User;

use App\Traits\ApiTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\EditUserRequest;
use App\Http\Requests\Api\User\NewUserRequest;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\User\ProfileUplaodImageRequest;
use App\Http\Resources\User\UserResource;
use App\Jobs\VerifyMailJob;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserController extends Controller
{
    use ApiTrait;

    const AWS_PROFILE_IMAGES_PATH = '/User/Profile/';

    public function userInfo()
    {
        try {
            return  $this->apiSuccessResponse(new UserResource(auth()->user()));
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function store(NewUserRequest $request)
    {

        try {

            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $code = $this->generateUniqueRandomNumber(20);
            $validatedData = array_merge($validatedData, ['code' => $code]);

            $user = User::create($validatedData);

            VerifyMailJob::dispatchSync($code, $request->get('email'));


            $token = $user->createToken('myApp')->plainTextToken;

            return $this->apiSuccessResponse(
                new UserResource($user),
                Response::HTTP_CREATED,
                ['vertify_message' => 'Please check email for vertify your emial', 'token' => $token]
            );
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

                return $this->apiSuccessResponse(new UserResource($user), Response::HTTP_OK, ['token' => $token]);
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
                    'password' => Hash::make($request->get('password')),
                ]);
            } else {
                auth()->user()->update($request->validated());
            }

            $updatedUser = User::find(auth()->id());

            return $this->apiSuccessResponse(
                new UserResource($updatedUser),
                Response::HTTP_OK,
                $hasPasspword  ? ['passwordMessage' => 'Your Password was updated successfully'] : []
            );
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

    public function uploadImage(ProfileUplaodImageRequest $request)
    {
        try {
            $file = $request->file('profile_image');
            $imageName = $this->createProfileImageName() . '.' . $request->profile_image->extension();

            $libraryModel =  auth()->user()->addMediaFromRequest('profile_image', $imageName)
                ->usingFileName($imageName)
                ->toMediaCollection('profile');

            if ($libraryModel instanceof Media) {
                return response()->json([
                    'error' => 0,
                    'message' => 'Image was uploaded successfully'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'error' => 1,
                    'message' => 'Something went wrong when uploading image to aws'
                ], Response::HTTP_OK);
            }
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }


    public function generateUniqueRandomNumber($length)
    {
        $number = '';
        do {
            $number = str_pad(random_int(0, (int)pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        } while (User::where('code', $number)->exists());
        return $number;
    }

    public function createProfileImageName()
    {
        return  str_replace('-', '_', Str::slug(auth()->user()->name)) . '_' . auth()->id();
    }
}
