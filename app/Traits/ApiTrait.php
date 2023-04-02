<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

trait ApiTrait
{
    public function exceptionResponse(\Exception $exception)
    {
        $response = [
            'error' => 1,
            'message' => $exception->getMessage(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
            'code' => $exception->getCode()
        ];
        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function apiSuccessResponse($data=null, $statuCode = Response::HTTP_OK, $message = null)
    {
        $basicData = ['error' => false ];

        if($data){
            $basicData ['data'] = $data;
        }

        if ($message) {
            $basicData['message']  = $message;
        }

        return response()->json($basicData, $statuCode);
    }

    public function returnWithMessag($message, $error = 1, $statu = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response()->json([
            'error' => $error,
            'message' => $message,
        ], $statu);
    }

    public function checkRequestKey($key, Request $request)
    {
        if (!$request->has($key)) {
            return $this->returnWithMessag("${key} is required field for this request");
        }
        return true;
    }

    public function apiRequestError(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => true,
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    public function generateUniqueRandomNumber($length)
    {
        $number = '';
        do {
            $number = str_pad(random_int(0, (int)pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        } while (User::where('code', $number)->exists());
        return $number;
    }
}
