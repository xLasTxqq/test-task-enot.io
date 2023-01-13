<?php

use App\Servises\PostgresqlDatabase;

require __DIR__ . '/../../vendor/autoload.php';

try {
    $database = new PostgresqlDatabase("database", "app", "5432", "root", "root");
    $database->connection();
    $sqlQueries = [
        "CREATE TABLE users (
        id SERIAL PRIMARY KEY,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        password varchar(255) NOT NULL);",

        "CREATE TABLE exchange_rates (
        code varchar(255) NOT NULL,
        name varchar(255) NOT NULL,
        rate float(30) NOT NULL);"
    ];
    foreach ($sqlQueries as $sqlQuery)
        $database->query($sqlQuery);
    echo "\nMigration completed\n";
} catch (\Exception $e) {
    print_r($e);
}
