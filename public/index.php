<?php
session_start();

use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\ProfileController;
use App\Controllers\RegistrationController;
use App\Servises\Parser;
use App\Servises\PostgresqlDatabase;
use App\Servises\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $prefix = '/backend';
    $request = $_REQUEST;
    $url = $_SERVER['REQUEST_URI'];
    if (mb_strpos($url, $prefix) === 0)
        $url = mb_substr($url, strlen($prefix));
    $method = $_SERVER['REQUEST_METHOD'];
    $database = new PostgresqlDatabase("database", "app", "5432", "root", "root");

    if (!isset($_SESSION['LAST_PARSE']) || (time() - $_SESSION['LAST_PARSE'] > 10800)) {
        Parser::handle($database);
        $_SESSION['LAST_PARSE'] = time();
    }

    echo match (true) {
        preg_match("/^\/login(\/){0,1}\$/", $url) && $method === "POST" => LoginController::create($request, $database),
        preg_match("/^\/register(\/){0,1}\$/", $url) && $method === "POST" => RegistrationController::create($request, $database),
        preg_match("/^\/logout(\/){0,1}\$/", $url) && $method === "DELETE" => LogoutController::destroy($request, $database),
        preg_match("/^\/\$/", $url) && $method === "GET" => ProfileController::index($request, $database),
        default => Response::pageNotFound(["Page $url not found"])
    };
} catch (Throwable $e) {
    Response::serverError();
}
