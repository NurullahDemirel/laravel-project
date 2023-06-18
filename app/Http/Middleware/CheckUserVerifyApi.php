<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckUserVerifyApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                ! is_null($request->user()->code))
        ) {
            return response()->json([
                'error' => 1,
                'error_message' => 'Please verify your email',
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
