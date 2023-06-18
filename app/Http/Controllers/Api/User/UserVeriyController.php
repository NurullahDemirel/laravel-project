<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserVeriyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $code)
    {

        try {
            $user = User::where('code', $code)->first();

            if (! $user) {
                return response()->json([
                    'error' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $user->update(['code' => null]);

            return response()->json([
                'error' => 0,
                'message' => 'Email address was verified successfully',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

    }
}
