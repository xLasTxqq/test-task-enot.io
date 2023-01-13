<?php

namespace App\Interfaces;

interface DatabaseInterface{
    function __construct(String $host, String $dbname, String $port, String $username, String $password);
    public function connection(): void;
    public function query($sql):array;
    public function getLastInsertId(): int;
}