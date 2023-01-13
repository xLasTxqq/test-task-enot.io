<?php

namespace App\Middlewares;

use App\Servises\Response;

class GuestMiddleware
{
    public static function handle()
    {
        if (!empty($_SESSION['user'])) Response::guestOnly();
    }
}
