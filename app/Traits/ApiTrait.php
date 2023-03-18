<?php
namespace App\Traits;

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
}
