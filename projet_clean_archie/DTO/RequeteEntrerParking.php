<?php

final class RequeteEntrerParking
{
    public function __construct(
        public readonly string $courrielClient,
        public readonly string $identifiantParking
    ) {}
}

final class ReponseEntrerParking
{
    public const SUCCES = 'success';
    public const CLIENT_INTROUVABLE = 'customer_not_found';
    public const PARKING_INTROUVABLE = 'parking_not_found';
    public const AUCUNE_RESERVATION = 'no_reservation';
    public const TROP_TOT = 'too_early';
    public const RESERVATION_TERMINEE = 'expired';
    public const ENTREE_DEJA_ENREGISTREE = 'already_entered';
    public const CLIENT_DEJA_STATIONNE = 'customer_already_parked';
    public const PARKING_COMPLET = 'parking_full';

    /** Résultat d’entrée sans entité métier. */
    public function __construct(
        public readonly string $statut,
        public readonly ?string $identifiantParking = null,
        public readonly ?string $horodatageEntree = null
    ) {}
}
