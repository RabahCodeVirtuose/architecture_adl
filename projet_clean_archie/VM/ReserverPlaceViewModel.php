<?php

class ReserverPlaceViewModel
{
    public string $prixAffiche;
    public bool $reussite;
    public string $message;

    public function __construct(string $prixAffiche, bool $reussite, string $message)
    {
        $this->prixAffiche = $prixAffiche;
        $this->reussite = $reussite;
        $this->message = $message;
    }
}
