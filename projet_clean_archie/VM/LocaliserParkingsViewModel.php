<?php

class LocaliserParkingsViewModel
{
    public array $parkings;
    public string $heureCalcul;

    public function __construct(array $parkings, string $heureCalcul)
    {
        $this->parkings = $parkings;
        $this->heureCalcul = $heureCalcul;
    }
}
