<?php

namespace App\Servises;

use App\Interfaces\DatabaseInterface;
use PDO;
use PDOException;

class PostgresqlDatabase implements DatabaseInterface
{
    private PDO $connection;

    function __construct(private String $host, private String $dbname, private String $port, private String $username, private String $password)
    {
    }

    public function connection(): void
    {

        try {
            $this->connection = new PDO(sprintf(
                "pgsql:host=%s port=%s dbname=%s user=%s password=%s",
                $this->host,
                $this->port,
                $this->dbname,
                $this->username,
                $this->password
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function query($sql): array
    {
        $query = $this->connection->query($sql);
        return $query->fetchAll();
    }

    public function getLastInsertId(): int{
        return $this->connection->lastInsertId();
    }
}
