<?php

class RequeteLocaliserParkings
{
    public float $latitudeCentre;
    public float $longitudeCentre;
    public float $rayonKm;

    public function __construct(
        float $latitudeCentre,
        float $longitudeCentre,
        float $rayonKm
    ) {
        $this->latitudeCentre = $latitudeCentre;
        $this->longitudeCentre = $longitudeCentre;
        $this->rayonKm = $rayonKm;
    }
}

class ReponseLocaliserParkings
{
    /** @var array<array<string, scalar|array>> Données de carte sans entités métier. */
    public array $parkingsDansLeRayon;
    public int $horodatageCalcul;

    public function __construct(array $parkingsDansLeRayon, int $horodatageCalcul)
    {
        $this->parkingsDansLeRayon = $parkingsDansLeRayon;
        $this->horodatageCalcul = $horodatageCalcul;
    }
}
