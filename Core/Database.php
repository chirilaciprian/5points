<?php

namespace Core;

use PDO;
use PDOStatement;

class Database
{
    public PDO $connection;
    public PDOStatement $statement;

    public function __construct(array $config, string $username = 'root', string $password = '')
    {
        $dsn = 'mysql:' . http_build_query($config, '', ';');

        $this->connection = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function query(string $query, array $params = []): static
    {
        $this->statement = $this->connection->prepare($query);

        foreach ($params as $key => $value) {
            $this->statement->bindValue(
                is_int($key) ? $key + 1 : $key,
                $value,
                match (true) {
                    is_int($value) => PDO::PARAM_INT,
                    is_bool($value) => PDO::PARAM_BOOL,
                    is_null($value) => PDO::PARAM_NULL,
                    default => PDO::PARAM_STR,
                }
            );
        }

        $this->statement->execute();

        return $this;
    }

    public function get(): array
    {
        return $this->statement->fetchAll();
    }

    public function find(): array|false
    {
        return $this->statement->fetch();
    }

    public function findOrFail(): array
    {
        $result = $this->find();

        if (!$result) {
            abort();
        }

        return $result;
    }

    public function value(): mixed
    {
        return $this->statement->fetchColumn();
    }
}
