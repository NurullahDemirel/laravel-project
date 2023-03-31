<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\FollowRequestResponse;
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
            return response()->json([
                'error' => 0,
                'message' => "User was {$process} successfully"
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->exceptionResponse($exception);
        }
    }

    public function responseFollower(ResponseFollowerRequest $request){
        try{

            $follower = Follower::find($request->get('follow_request_id'));

            if(auth()->id() != $follower->follow_to){
                return $this->returnWithMessag('This request is not yours');
            }


            if($follower->is_accepted){
                return $this->returnWithMessag('This request already accepted');
            }

            $response = $request->get('response');

            if($response == FollowRequestResponse::Accep->value){
                $follower->update(['is_accepted' =>1]);
                $process = 'Accepted';
            }
            else if($response == FollowRequestResponse::Reject->value){
                //todo delete request s
                $follower->update(['is_accepted' =>0]);
                $process = 'Rejected';;
            }

            return $this->apiSuccessResponse("request {$process} successfully",1, Response::HTTP_OK);

        }catch(\Exception $exception){
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
