<?php

final class ReserverPlaceUseCase
{
    public function __construct(
        private readonly IRepositoryClientParCourriel $depotClients,
        private readonly IRepositoryParking $depotParkings,
        private readonly IRepositoryReservations $depotReservations,
        private readonly IRepositoryStationnements $depotStationnements
    ) {}

    public function executer(RequeteReserverPlace $requete, PortSortieReservation $sortie): void
    {
        if ($requete->debut >= $requete->fin) {
            $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::DATES_INVALIDES));
            return;
        }

        $parking = $this->depotParkings->trouverParkingParId($requete->identifiantParking);
        if ($parking === null) {
            $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::PARKING_INTROUVABLE));
            return;
        }

        $client = $this->depotClients->trouverClientParCourriel($requete->courrielClient);
        if ($client === null) {
            $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::CLIENT_INTROUVABLE));
            return;
        }

        if (!$parking->estOuvertPendant($requete->debut, $requete->fin)) {
            $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::HORS_HORAIRES_OUVERTURE));
            return;
        }

        $reservationsEnConflit = $this->depotReservations->trouverReservationsEnConflit(
            $parking->identifiant(),
            $requete->debut,
            $requete->fin
        );
        $stationnementsOuverts = $this->depotStationnements->trouverStationnementsOuvertsParParking(
            $parking->identifiant()
        );
        $occupationMaximale = $parking->occupationMaximaleSimultanee(
            $requete->debut,
            $requete->fin,
            $reservationsEnConflit,
            $stationnementsOuverts
        );

        if (!$parking->peutAccepterReservation($occupationMaximale)) {
            $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::CAPACITE_INSUFFISANTE));
            return;
        }

        $prixCentimes = $parking->calculerPrixCentimes($requete->debut, $requete->fin);
        $reservation = new Reservation(
            bin2hex(random_bytes(12)),
            $client->courriel(),
            $parking->identifiant(),
            $requete->debut,
            $requete->fin
        );

        $this->depotReservations->enregistrer($reservation);
        $sortie->transmettre(new ReponseReserverPlace(ReponseReserverPlace::SUCCES, $prixCentimes));
    }
}
