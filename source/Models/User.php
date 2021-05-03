<?php

namespace Source\Models;

use Source\Core\Model;

class User extends Model
{

    /**
     * User constructor
     */
    public function __construct()
    {
        parent::__construct("users", ["id"], ["name"]);
    }

    /**
     * Bootstrap the User model instance
     */
    public function bootstrap(string $name): User
    {
        $this->name = $name;
        return $this;
    }
}
