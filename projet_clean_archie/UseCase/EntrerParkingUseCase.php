<?php

final class EntrerParkingUseCase
{
    public function __construct(
        private readonly IRepositoryClientParCourriel $clients,
        private readonly IRepositoryParking $parkings,
        private readonly IRepositoryReservations $reservations,
        private readonly IRepositoryStationnements $stationnements,
        private readonly Horloge $horloge
    ) {}

    public function executer(RequeteEntrerParking $requete, PortSortieEntreeParking $sortie): void
    {
        $client = $this->clients->trouverClientParCourriel($requete->courrielClient);
        if ($client === null) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::CLIENT_INTROUVABLE));
            return;
        }

        $parking = $this->parkings->trouverParkingParId($requete->identifiantParking);
        if ($parking === null) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::PARKING_INTROUVABLE));
            return;
        }

        $reservationsDuClient = $this->reservations->trouverReservationsClientParking(
            $client->courriel(),
            $parking->identifiant()
        );
        if ($reservationsDuClient === []) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::AUCUNE_RESERVATION));
            return;
        }

        $maintenant = $this->horloge->maintenant();
        $reservationActive = null;
        $reservationFuture = false;
        foreach ($reservationsDuClient as $reservation) {
            if ($maintenant >= $reservation->debut() && $maintenant < $reservation->fin()) {
                $reservationActive = $reservation;
                break;
            }
            if ($maintenant < $reservation->debut()) {
                $reservationFuture = true;
            }
        }
        if ($reservationActive === null) {
            $motif = $reservationFuture
                ? ReponseEntrerParking::TROP_TOT
                : ReponseEntrerParking::RESERVATION_TERMINEE;
            $sortie->transmettre(new ReponseEntrerParking($motif));
            return;
        }
        if ($this->stationnements->trouverStationnementOuvertParReservation($reservationActive->identifiant()) !== null) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::ENTREE_DEJA_ENREGISTREE));
            return;
        }
        if ($this->stationnements->trouverStationnementOuvertParClient($client->courriel()) !== null) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::CLIENT_DEJA_STATIONNE));
            return;
        }

        $stationnementsOuverts = $this->stationnements->trouverStationnementsOuvertsParParking($parking->identifiant());
        if (!$parking->peutAccepterEntreePhysiqueA($maintenant, $stationnementsOuverts)) {
            $sortie->transmettre(new ReponseEntrerParking(ReponseEntrerParking::PARKING_COMPLET));
            return;
        }

        $stationnement = new Stationnement(
            bin2hex(random_bytes(12)),
            $reservationActive->identifiant(),
            $client->courriel(),
            $parking->identifiant(),
            $maintenant
        );
        $this->stationnements->enregistrer($stationnement);
        $sortie->transmettre(new ReponseEntrerParking(
            ReponseEntrerParking::SUCCES,
            $parking->identifiant(),
            $maintenant->format(DateTimeInterface::ATOM)
        ));
    }
}
