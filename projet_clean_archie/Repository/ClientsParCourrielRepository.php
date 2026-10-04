<?php

class ClientsParCourrielRepository implements IRepositoryClientParCourriel
{
    /** @param Client[] $clients */
    public function __construct(private array $clients)
    {
    }

    public function trouverClientParCourriel(string $courriel): ?Client
    {
        foreach ($this->clients as $client) {
            if ($client->courriel() === $courriel) {
                return $client;
            }
        }

        return null;
    }

    /** @return Client[] */
    public function tous(): array
    {
        return $this->clients;
    }
}
