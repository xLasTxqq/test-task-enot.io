<?php

namespace App\Controllers;

use App\Interfaces\DatabaseInterface;
use App\Middlewares\GuestMiddleware;
use App\Servises\Response;
use App\Servises\Validator;
use PDO;

class RegistrationController
{
    public static function create(array|null $request, DatabaseInterface $databaseInterface): void
    {
        GuestMiddleware::handle();
        $rules = [
            "name" => [
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED,
                Validator::VALIDATOR_MIN => 5
            ],
            "email" => [
                Validator::VALIDATOR_EMAIL,
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED,
                Validator::VALIDATOR_UNIQUE => ['users', 'email']
            ],
            "password" => [
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_MIN => 8,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED
            ],
            "password_confirmation" => [
                Validator::VALIDATOR_MAX => 255,
                Validator::VALIDATOR_MIN => 8,
                Validator::VALIDATOR_NOT_EMPTY,
                Validator::VALIDATOR_REQUERED,
                Validator::VALIDATOR_PASSWORD_CONFIRMED
            ]
        ];

        $data = Validator::validate($request, $rules, $databaseInterface);
        $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT, ['cost' => 10]);
        $databaseInterface->connection();
        $sql = "INSERT INTO users (name, email, password) VALUES ('{$data['name']}', '{$data['email']}', '{$data['password']}')";
        $databaseInterface->query($sql);
        $id = $databaseInterface->getLastInsertId();

        $_SESSION['user'] = [
            "id" => $id,
            "name" => $data['name'],
            "email" => $data['email']
        ];

        Response::success();
    }
}
