<?php

namespace Source\Core;

class Model
{
    protected ?\PDOException $fail;

    /** Table data */
    protected ?object $data;

    /** Database table */
    protected static string $entity;

    /** Variables to not update or create */
    protected static array $protected;

    /** Required fields */
    protected static array $required;

    /**
     * __construct
     *
     * @param  string $entity Database table
     * @param  array $protected Variables no update or create
     * @param  array $required Required fields
     * @return void
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        self::$entity = $entity;
        self::$protected = $protected;
        self::$required = $required;
    }

    /**
     * __set
     *
     * @param  mixed $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * __isset
     *
     * @param  mixed $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->data->$name);
    }

    /**
     * Return all model's data
     *
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * Get the fail object
     *
     * @return \PDOException|null
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * Create a record
     *
     * @param  string $entity
     * @param  array $data
     * @return int|null
     */
    public function create(array $data): ?int
    {
        try {
            /**
             *  Get the keys of the array and transform in a string.
             *  Ex.: ['id' => 2, 'name' => 'Master'] turned into id, name
             */
            $columns = implode(', ', array_keys($data));

            /**
             * Now we add the binds for the PDO using the character ':'
             * Ex.: ['id' => 2, 'name' => 'Master'] turned into :id, :name
             */
            $values = ':' . implode(', :', array_keys($data));

            /**
             * Prepare the PDO statement using the final query
             * Ex.: INSERT INTO users (id, name) VALUES (:id, :name)
             */
            $stmt = Connect::getInstance()
                ->prepare(
                    "INSERT INTO " . self::$entity . " ({$columns}) VALUES ({$values})"
                );

            /**
             * Add the values that will be replaced in the text with ':' character
             * Ex.: INSERT ... VALUES (2, 'Master')
             */
            $stmt->execute($this->safe($this->filter($data)));

            /**
             * Return the last insert id into the database
             */
            return (int) Connect::getInstance()->lastInsertId();
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Get a record
     *
     * @param  string $select
     * @param  string|null $params
     * @return \PDOStatement|null
     */
    public function read(string $select, string $params = null): ?\PDOStatement
    {
        try {
            $stmt = Connect::getInstance()->prepare($select);

            if ($params) {
                parse_str($params, $paramsList);
                foreach ($paramsList as $key => $value) {
                    if ($key == 'limit' || $key == 'offset') {
                        $stmt->bindValue(":{$key}", $value, \PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(":{$key}", $value, \PDO::PARAM_STR);
                    }
                }
            }

            $stmt->execute();
            return $stmt;
        } catch (\Exception $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Update a record
     *
     * @param  array $data
     * @param  string $terms
     * @param  string $params
     * @return int|null
     */
    public function update(array $data, string $terms, string $params): ?int
    {
        try {
            $dataSet = [];

            foreach ($this->safe($data) as $bind => $value) {
                $dataSet[] = "{$bind} = :$bind";
            }

            $dataSet = implode(", ", $dataSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE " . self::$entity . " SET {$dataSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));

            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * delete
     *
     * @param  string $terms
     * @param  string $params
     * @return int
     */
    public function delete(string $terms, string $params): ?int
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM " . self::$entity . " WHERE {$terms}");
            parse_str($params, $params);
            $stmt->execute($params);

            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Remove if a protected attribute is trying to be set
     *
     * @return array|null
     */
    public function safe(): ?array
    {
        $safe = (array) $this->data;
        foreach (static::$protected as $unset) {
            unset($safe[$unset]);
        }

        return $safe;
    }

    /**
     * Filter the variables
     *
     * @param  array $data
     * @return array
     */
    public function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS));
        }

        return $filter;
    }

    /**
     * Verify if all required variables are present
     *
     * @return bool
     */
    public function required(): bool
    {
        $required = (array) $this->data;
        foreach (static::$required as $field) {
            if (empty($required[$field])) {
                return false;
            }
        }

        return true;
    }
}
