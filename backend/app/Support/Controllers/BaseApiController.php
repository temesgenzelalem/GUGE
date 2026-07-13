<?php

namespace App\Support\Controllers;

use App\Support\ApiResponse;
use Illuminate\Routing\Controller as BaseController;

abstract class BaseApiController extends BaseController
{
    protected function success(mixed $data = null, string $message = 'Success', array $meta = [], array $links = [], int $status = 200)
    {
        return ApiResponse::success($data, $message, $meta, $links, $status);
    }

    protected function error(string $message, int $status = 400, array $errors = [])
    {
        return ApiResponse::error($message, $status, $errors);
    }
}
