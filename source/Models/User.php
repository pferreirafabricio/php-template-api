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
}
