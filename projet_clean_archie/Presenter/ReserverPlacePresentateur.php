<?php

class ReserverPlacePresentateur implements PortSortieReservation
{
    public ReserverPlaceViewModel $modeleVue;

    public function transmettre(ReponseReserverPlace $resultat): void
    {
        $message = match ($resultat->statut) {
            ReponseReserverPlace::SUCCES => 'Réservation confirmée !',
            ReponseReserverPlace::DATES_INVALIDES => 'La date de fin doit être postérieure à la date de début.',
            ReponseReserverPlace::PARKING_INTROUVABLE => 'Parking introuvable.',
            ReponseReserverPlace::CLIENT_INTROUVABLE => 'Client introuvable.',
            ReponseReserverPlace::CAPACITE_INSUFFISANTE => 'Le parking est complet pour cette période.',
            ReponseReserverPlace::HORS_HORAIRES_OUVERTURE => 'Le parking n’est pas ouvert pendant toute cette période.',
            default => throw new LogicException('Résultat de réservation inconnu.'),
        };

        $prixAffiche = number_format($resultat->prixCentimes / 100, 2, ',', ' ') . ' €';
        $this->modeleVue = new ReserverPlaceViewModel(
            $prixAffiche,
            $resultat->reussite,
            $message
        );
    }
}
