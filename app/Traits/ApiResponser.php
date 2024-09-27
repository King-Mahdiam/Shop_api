<?php

namespace App\Traits;

trait ApiResponser {
    public function SuccessResponse($data , $code , $massage = null)
    {
        return response()->json([
            'status' =>  'success' ,
            'massage' => $massage ,
            'data' => $data
        ] , $code);
    }

    public function ErrorResponse($code , $massage = null)
    {
        return response()->json([
            'status' =>  'error' ,
            'massage' => $massage ,
            'data' => ''
        ] , $code);
    }
}
