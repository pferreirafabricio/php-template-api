<?php

namespace Source\Core;

class Connect
{
    public static ?\PDO $instance;

    /** @var array @const array */
    private const OPTIONS = [
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_CASE => \PDO::CASE_NATURAL
    ];

    /**
     * Get the current stance of the database
     */
    public static function getInstance(): ?\PDO
    {
        if (!empty(self::$instance)) {
            return self::$instance;
        }

        try {
            self::$instance = new \PDO(
                "mysql:host=" . env('DB_HOST') . ";port=" . env('DB_PORT') . ";dbname=" . env('DB_NAME'),
                env('DB_USER'),
                env('DB_PASSWORD'),
                self::OPTIONS
            );
        } catch (\PDOException $exception) {
            echo $exception->getMessage();
            die("<h1>Something was wrong to connect to database</h1>");
        }

        return self::$instance;
    }

    /**
     * Connect constructor.
     */
    private function __construct()
    {
    }

    /**
     * Connect clone.
     */
    private function __clone()
    {
    }
}
