<?php

namespace Source\Controllers;

use Source\Models\User;

class UserController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index(): string
    {
        return response($this->user->find()->fetch(true))->json();
    }
}
