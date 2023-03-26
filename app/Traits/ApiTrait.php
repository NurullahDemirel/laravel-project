<?php

namespace App\Traits;

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
            'code' => $exception->getCode()
        ];
        return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function apiSuccessResponse($data, $statuCode = Response::HTTP_OK, $additionalData = [])
    {
        $basicData = [
            'error' => false,
            'data' => $data,
        ];
        $basicData = !empty($additionalData) ? array_merge($basicData, $additionalData) : $data;

        return response()->json($basicData, $statuCode);
    }

    public function apiErrorResponse($message, $statu = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response()->json([
            'error' => true,
            'message' => $message,
        ], $statu);
    }

    public function checkRequestKey($key,Request $request){
        if(!$request->has($key)){
            return $this->apiErrorResponse("{key} is required field for this request");
        }
        return true;
    }

    public function apiRequestError(Validator $validator){
        throw new HttpResponseException(response()->json([
            'error' => true,
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
