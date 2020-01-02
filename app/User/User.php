<?php

namespace SimpleBase\User;

class User implements UserInterface
{
    public function logIn($password)
    {
        if ($password === getenv('password')) {
            $_SESSION['auth'] = true;
        }

        return $this;
    }

    public function isAuthorized(): bool
    {
        return (bool) ($_SESSION['auth'] ?? false);
    }
}
