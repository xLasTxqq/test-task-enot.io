<?php

namespace App\Servises;

class Response
{
    private const PAGE_NOT_FOUND = 404;
    private const SERVER_ERROR = 500;
    private const SUCCESS = 200;
    private const BAD_REQUEST = 400;
    private const UNAUTHORIZED = 401;
    private const GUEST_ONLY = 302;

    private static function json($status, $data = [], $errors = [])
    {
        header("Content-Type:application/json; charset=utf-8");
        $responseArray = [
            "status" => $status
        ];
        if (!empty($data))
            $responseArray["data"] = $data;
        if (!empty($errors))
            $responseArray["errors"] = $errors;

        echo json_encode($responseArray);
        exit;
    }

    public static function pageNotFound($errors = ['Page not found'])
    {
        self::json(self::PAGE_NOT_FOUND, errors: $errors);
    }

    public static function serverError($errors = ['Something went wrong, please try later'])
    {
        self::json(self::SERVER_ERROR, errors: $errors);
    }

    public static function badRequest($errors = ['Data is invalid'])
    {
        self::json(self::BAD_REQUEST, errors: $errors);
    }

    public static function unauthorized($errors = ['You must be logged in'])
    {
        self::json(self::UNAUTHORIZED, errors: $errors);
    }

    public static function guestOnly($errors = ['You must not be logged in'])
    {
        self::json(self::GUEST_ONLY, errors: $errors);
    }

    public static function success($data = [])
    {
        self::json(self::SUCCESS, data: $data);
    }
}
