<?php

namespace App\Controllers;

use App\Interfaces\DatabaseInterface;
use App\Middlewares\GuestMiddleware;
use App\Servises\Response;
use App\Servises\Validator;

class LoginController
{
    public static function create(array|null $request, DatabaseInterface $databaseInterface): void
    {
        GuestMiddleware::handle();
        $rules = [
            "email" => [
                Validator::VALIDATOR_EMAIL,
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED,
            ],
            "password" => [
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_MIN => 8,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED
            ],
        ];

        $data = Validator::validate($request, $rules);
        $databaseInterface->connection();
        $sql = "SELECT id, name, email, password FROM users WHERE email = '{$data['email']}' LIMIT 1";
        $dbData = $databaseInterface->query($sql);
        if (empty($dbData) || !password_verify(
            $data['password'],
            $dbData[0]['password']
        )) Response::badRequest(['Email or password incorect']);
        $_SESSION['user'] = [
            "id" => $dbData[0]['id'],
            "name" => $dbData[0]['name'],
            "email" => $dbData[0]['email']
        ];

        Response::success();
    }
}
