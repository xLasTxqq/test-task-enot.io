<?php

namespace App\Controllers;

use App\Interfaces\DatabaseInterface;
use App\Middlewares\AuthMiddleware;
use App\Servises\Response;

class ProfileController
{
    public static function index(array|null $request, DatabaseInterface $databaseInterface): void
    {
        AuthMiddleware::handle();
        $databaseInterface->connection();
        $sql = "SELECT * FROM exchange_rates";
        $data = $databaseInterface->query($sql);
        Response::success($data);
    }
}
