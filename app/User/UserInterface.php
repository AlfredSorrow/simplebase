<?php

namespace SimpleBase\User;

interface UserInterface
{
    public function logIn($password);
    public function isAuthorized(): bool;
}
