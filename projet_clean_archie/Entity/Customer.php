<?php

namespace App\Domain\Entity;

class Customer
{
    public function __construct(
        private readonly string $id,
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function verifyPassword(string $hashedPassword): bool
    {
        return password_verify($this->password, $hashedPassword);
    }
}
