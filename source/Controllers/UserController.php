<?php

namespace Source\Controllers;

use Source\Models\User;
use Source\Support\Response;

class UserController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index(): string
    {
        return response($this->user->getAll())->json();
    }
}
