<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiTrait
{
    public function exceptionResponse(\Exception $exception)
    {
        $response = [
            'error' => 1,
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ];
        return response()->json($response);
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
}
