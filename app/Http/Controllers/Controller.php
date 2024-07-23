<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="mementos-backend",
 *     version="1.0.0",
 *     description="Documentation de l'API du projet Mementos",
 *     @OA\Contact(
 *       email="support@example.com",
 *       name="Support Team"
 *     )
 *   ),
 *   @OA\Server(
 *       url=L5_SWAGGER_CONST_HOST,
 *       description="Demo API Server"
 *   )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
