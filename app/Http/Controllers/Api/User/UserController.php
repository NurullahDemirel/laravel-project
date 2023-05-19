<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\FollowRequestResponse;
use App\Events\AccepRequest as EventsAccepRequest;
use App\Events\TestEvent;
use App\Models\User;
use App\Models\Follower;
use App\Traits\ApiTrait;
use App\Jobs\VerifyMailJob;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\FollowRequest;
use App\Http\Resources\Api\User\UserResource;
use App\Http\Requests\Api\User\NewUserRequest;
use App\Http\Requests\Api\User\EditUserRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Http\Requests\Api\User\ProfileUplaodImageRequest;
use App\Http\Requests\Api\Follower\ResponseFollowerRequest;
use App\Notifications\AccepRequest;

class UserController extends Controller
{
    use ApiTrait;

    public function userInfo()
    {
        try {
            return  $this->apiSuccessResponse(['users' => new UserResource(auth()->user())]);
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
                [
                    'user' => new UserResource($user),
                    'vertify_message' => 'Please check email for vertify your emial',
                    'token' => $token
                ],
                Response::HTTP_CREATED,
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

                return $this->apiSuccessResponse(['user' => new UserResource($user), 'token' => $token], Response::HTTP_OK);
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
                ['user' => new UserResource($updatedUser)],
                Response::HTTP_OK,
                $hasPasspword  ? 'Your Password was updated successfully' : null
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

    public function follow(FollowRequest $followRequest)
    {
        try {
            $existsRequestBefore = Follower::where([['follow_by', '=', auth()->id()], ['follow_to', '=', $followRequest->get('follow_id')]])
                ->first();

            if (!$existsRequestBefore) {
                //send request
                Follower::create([
                    'follow_by' => auth()->id(),
                    'follow_to' => $followRequest->get('follow_id')
                ]);
                $process = 'send follow_request';
            } else {
                $process = $existsRequestBefore->is_accepted ? 'unfollowed' : 'backed to request';
                $existsRequestBefore->delete();
            }

            return $this->apiSuccessResponse(null, Response::HTTP_OK, "User was {$process} successfully");
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function responseFollower(ResponseFollowerRequest $request)
    {
        try {
            $followerRequest = Follower::find($request->get('follow_request_id'));

            $follower = User::find($followerRequest->follow_by);

            if (auth()->id() != $followerRequest->follow_to) {
                return $this->returnWithMessag('This request is not yours');
            }


            if ($followerRequest->is_accepted) {
                return $this->returnWithMessag('This request already accepted');
            }

            $response = $request->get('response');


            if ($response == FollowRequestResponse::Accept->value) {
                // TestEvent::dispatch();
                // $followerRequest->update(['is_accepted' => 1]);
                $follower->notify(new AccepRequest(auth()->user()));
                // broadcast(new EventsAccepRequest(auth()->user()));
                $process = 'Accepted';
            } else if ($response == FollowRequestResponse::Reject->value) {
                $followerRequest->delete();
                $process = 'Rejected';;
            }

            return $this->apiSuccessResponse(null, Response::HTTP_OK, "request {$process} successfully");
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function createProfileImageName()
    {
        return  str_replace('-', '_', Str::slug(auth()->user()->name)) . '_' . auth()->id();
    }
}
