<?php

final class EntrerParkingPresentateur implements PortSortieEntreeParking
{
    public EntreeParkingViewModel $modeleVue;

    public function transmettre(ReponseEntrerParking $reponse): void
    {
        $message = match ($reponse->statut) {
            ReponseEntrerParking::SUCCES => 'Entrée enregistrée. La porte est considérée comme ouverte.',
            ReponseEntrerParking::CLIENT_INTROUVABLE => 'Client fictif introuvable.',
            ReponseEntrerParking::PARKING_INTROUVABLE => 'Parking introuvable.',
            ReponseEntrerParking::AUCUNE_RESERVATION => 'Aucune réservation ne correspond à ce client et ce parking.',
            ReponseEntrerParking::TROP_TOT => 'La réservation n’a pas encore commencé.',
            ReponseEntrerParking::RESERVATION_TERMINEE => 'La réservation est terminée.',
            ReponseEntrerParking::ENTREE_DEJA_ENREGISTREE => 'Une entrée est déjà enregistrée pour cette réservation.',
            ReponseEntrerParking::CLIENT_DEJA_STATIONNE => 'Ce client a déjà un stationnement en cours.',
            ReponseEntrerParking::PARKING_COMPLET => 'Entrée impossible : toutes les places sont actuellement occupées.',
            default => throw new LogicException('Résultat d’entrée inconnu.'),
        };
        $this->modeleVue = new EntreeParkingViewModel($reponse->statut === ReponseEntrerParking::SUCCES, $message);
    }
}
