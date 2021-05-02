<?php

namespace Source\Core;

abstract class Model
{
    /** Table name */
    protected string $entity;

    /** Variables to not update or create */
    protected array $protected;

    /** Required fields */
    protected array $required;

    /** Table data */
    protected ?object $data = null;

    protected string $query;

    protected array $conditionsParams = [];

    protected string $order = "";

    protected string $limit = "";

    protected string $offset = "";

    protected ?\PDOException $fail = null;

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
        $this->entity = $entity;
        $this->protected = array_merge($protected, ['created_at', 'updated_at']);
        $this->required = $required;
    }

    public function __set(mixed $name, mixed $value): void
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    public function __isset(mixed $name): bool
    {
        return isset($this->data->$name);
    }

    public function __get(mixed $name): mixed
    {
        return ($this->data->$name ?? null);
    }

    /**
     * Return all model's data
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * Get the fail object
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * Find a record given the conditions
     *
     * @param string|null $conditions @example "age = :age"
     * @param string|null $conditionsParams @example "age=12"
     * @param string $columns @example "id, name"
     */
    public function find(?string $conditions = null, ?string $conditionsParams = null, string $columns = "*"): Model
    {
        if ($conditions) {
            $this->query = "SELECT {$columns} FROM {$this->entity} WHERE {$conditions}";
            parse_str($conditionsParams, $this->conditionsParams);
            return $this;
        }

        $this->query = "SELECT {$columns} FROM {$this->entity}";
        return $this;
    }

    /**
     * Set the order by of the query
     *
     * @param string $columnName
     */
    public function order(string $columnName): Model
    {
        $this->order = " ORDER BY {$columnName}";
        return $this;
    }

    /**
     * Set the offset of the query
     *
     * @param integer $offset
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * Limit the number of records
     *
     * @param integer $limit
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * Find a record by the id column
     *
     * @param integer $id
     * @param string $columns
     */
    public function findById(int $id, string $columns = "*"): mixed
    {
        $find = $this->find('id = :id', "id={$id}", $columns);
        return $find->fetch();
    }

    /**
     * Execute the mounted query
     *
     * @param boolean $all
     * @return mixed|null
     */
    public function fetch(bool $all = false): mixed
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->mountQuery());
            $stmt->execute($this->conditionsParams);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Count the number of records
     */
    public function count(): int
    {
        $stmt = Connect::getInstance()->prepare($this->mountQuery());
        $stmt->execute($this->conditionsParams);
        return $stmt->rowCount();
    }

    /**
     * Create a new record
     *
     * @param array $data @example ['name' => 'Noa', 'age' => 23]
     */
    public function create(array $data): ?int
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return (int) Connect::getInstance()->lastInsertId();
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Update a record by conditions
     *
     * @param array $data @example "['name' => 'Mike']"
     * @param string $conditions @example "id = :id"
     * @param string $conditionsParams @example "id=1"
     */
    public function update(array $data, string $conditions, string $conditionsParams): ?int
    {
        try {
            $dataSet = [];
            foreach ($data as $bind => $value) {
                $dataSet[] = "{$bind} = :{$bind}";
            }
            $dataSet = implode(', ', $dataSet);
            parse_str($conditionsParams, $paramsList);

            $stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dataSet} WHERE {$conditions}");
            $stmt->execute($this->filter(array_merge($data, $paramsList)));
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * Delete a record given a condition
     *
     * @param string $conditions @example "name = :name"
     * @param string|null $conditionsParams @example "name=john"
     */
    public function delete(string $conditions, ?string $conditionsParams): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$conditions}");

            if ($conditionsParams) {
                parse_str($conditionsParams, $paramsList);
                $stmt->execute($paramsList);
                return true;
            }

            $stmt->execute();
            return true;
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * Remove a record by id
     */
    public function destroy(): bool
    {
        if (empty($this->id)) {
            return false;
        }

        return $this->delete('id = :id', "id={$this->id}");
    }


    /**
     * Remove if a protected attribute is trying to be set
     */
    public function safe(): ?array
    {
        $safe = (array) $this->data;
        foreach ($this->protected as $unset) {
            unset($safe[$unset]);
        }

        return $safe;
    }

    /**
     * Filter the variables
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
     */
    public function required(): bool
    {
        $required = (array) $this->data;
        foreach ($this->required as $field) {
            if (empty($required[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mount the SQL query with all commands
     */
    private function mountQuery(): string
    {
        return $this->query . $this->order . $this->limit . $this->offset;
    }
}
