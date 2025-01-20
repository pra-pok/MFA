<?php

namespace App\Utils;

use App\Dtos\ResponseDTO;

class ResponseUtil
{

    public static function wrapResponse(ResponseDTO $response)
    {
        return response()->json($response);
    }

}
