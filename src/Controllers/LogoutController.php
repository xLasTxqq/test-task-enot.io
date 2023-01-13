<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Servises\Response;

class LogoutController
{
    public static function destroy(): void
    {
        AuthMiddleware::handle();
        unset($_SESSION['user']);
        Response::success();
    }
}
