<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Config\Database;
use PDO;

abstract class BaseRepository
{
    protected PDO $database;


    public function __construct()
    {
        $this->database = Database::getConnection();
    }


    protected function query(
        string $sql,
        array $parameters = []
    ): \PDOStatement {

        $statement = $this->database->prepare($sql);

        $statement->execute($parameters);

        return $statement;
    }


    protected function fetchOne(
        string $sql,
        array $parameters = []
    ): ?array {

        $result = $this->query(
            $sql,
            $parameters
        )->fetch(PDO::FETCH_ASSOC);


        return $result ?: null;
    }


    protected function fetchAll(
        string $sql,
        array $parameters = []
    ): array {

        return $this->query(
            $sql,
            $parameters
        )->fetchAll(PDO::FETCH_ASSOC);
    }


    protected function insert(
        string $sql,
        array $parameters = []
    ): int {

        $this->query(
            $sql,
            $parameters
        );

        return (int)$this->database->lastInsertId();
    }
}