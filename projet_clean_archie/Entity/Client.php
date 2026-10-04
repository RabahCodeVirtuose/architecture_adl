<?php

class Customer
{
    public function __construct(
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName
    ) {}

    public function courriel(): string
    {
        return $this->email;
    }

    public function prenom(): string
    {
        return $this->firstName;
    }

    public function nomFamille(): string
    {
        return $this->lastName;
    }

    public function verifierMotDePasse(string $motDePasseHache): bool
    {
        return password_verify($this->password, $motDePasseHache);
    }
}

// Alias français pour le code courant ; le nom historique reste utile aux sessions sérialisées.
class_alias(Customer::class, 'Client');
