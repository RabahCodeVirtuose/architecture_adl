<?php

class RequeteReserverPlace
{
    public string $courrielClient;
    public string $identifiantParking;
    public DateTimeImmutable $debut;
    public DateTimeImmutable $fin;

    public function __construct(
        string $courrielClient,
        string $identifiantParking,
        DateTimeImmutable $debut,
        DateTimeImmutable $fin
    ) {
        $this->courrielClient = $courrielClient;
        $this->identifiantParking = $identifiantParking;
        $this->debut = $debut;
        $this->fin = $fin;
    }
}

class ReponseReserverPlace
{
    public const SUCCES = 'success';
    public const DATES_INVALIDES = 'invalid_dates';
    public const PARKING_INTROUVABLE = 'parking_not_found';
    public const CLIENT_INTROUVABLE = 'customer_not_found';
    public const CAPACITE_INSUFFISANTE = 'capacity_unavailable';
    public const HORS_HORAIRES_OUVERTURE = 'outside_opening_hours';

    public string $statut;
    public bool $reussite;
    public int $prixCentimes;

    public function __construct(string $statut, int $prixCentimes = 0)
    {
        $this->statut = $statut;
        $this->reussite = $statut === self::SUCCES;
        $this->prixCentimes = $prixCentimes;
    }
}
