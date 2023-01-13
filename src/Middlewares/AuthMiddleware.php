<?php

namespace App\Middlewares;

use App\Servises\Response;

class AuthMiddleware
{
    public static function handle()
    {
        if (empty($_SESSION['user'])) Response::unauthorized();
    }
}