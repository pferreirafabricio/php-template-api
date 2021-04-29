<?php

namespace Source\Models;

use Source\Core\Model;

class User extends Model
{

    /**
     * User constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct("users", ["id"], ["name"]);
    }

    /**
     * Bootstrap the User model instance
     *
     * @param  string $name
     * @return User
     */
    public function bootstrap(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Generic method for find record(s)
     *
     * @param  string $terms
     * @param  string $params
     * @param  string $columns
     * @return User|null
     */
    public function find(string $terms, string $params, string $columns = "*"): ?User
    {
        $find = $this->read("SELECT {$columns} FROM " . self::$entity . " WHERE {$terms}", $params);

        if ($this->fail() || !$find->rowCount()) {
            return null;
        }

        return $find->fetchObject(__CLASS__);
    }

    /**
     * Get all records
     *
     * @return array|null
     */
    public function getAll(): ?array
    {
        $get = $this->read("SELECT * FROM " . self::$entity);

        if ($this->fail() || !$get->rowCount()) {
            return null;
        }

        return $get->fetchAll();
    }

    /**
     * Find a user by Id
     *
     * @param  int $id
     * @param  string $columns
     * @return User
     */
    public function findById(int $id, string $columns = "*"): ?User
    {
        return $this->find("id = :id", "id={$id}", $columns);
    }

    /**
     * Update a record by id
     *
     * @param  array $data
     * @param  int $id
     * @return int
     */
    public function updateById(array $data, int $id): ?int
    {
        return $this->update($data, "id = :id", "id={$id}");
    }

    /**
     * Remove a record by id
     *
     * @param  int $id
     * @return int
     */
    public function remove(int $id): ?int
    {
        return $this->delete("id = :id", "id={$id}");
    }
}
