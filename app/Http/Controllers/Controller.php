<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0",
 *         title="My Free Admission Api",
 *         description="My Free Admission Api",
 *     )
 *
 * )
 * @OA\Server(
 *     url="http://127.0.0.1:8000/",
 *     description="API server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

