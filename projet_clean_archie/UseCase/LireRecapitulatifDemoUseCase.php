<?php

/** Prépare les données simples du récapitulatif affiché dans la page. */
final class LireRecapitulatifDemoUseCase
{
    public function __construct(
        private readonly IRepositoryListeParkings $parkings,
        private readonly IRepositoryClientParCourriel $clients,
        private readonly IRepositoryReservations $reservations,
        private readonly IRepositoryStationnements $stationnements
    ) {}

    public function executer(RequeteRecapitulatifDemo $requete, PortSortieRecapitulatifDemo $sortie): void
    {
        $lignesParkings = [];
        foreach ($this->parkings->listerParkings() as $parking) {
            $lignesParkings[] = ['id' => $parking->identifiant(), 'name' => $parking->nom()];
        }
        $lignesClients = [];
        foreach ($this->clients->tous() as $client) {
            $lignesClients[] = [
                'email' => $client->courriel(),
                'name' => $client->prenom() . ' ' . $client->nomFamille(),
            ];
        }
        $lignesReservations = [];
        foreach ($this->reservations->tous() as $reservation) {
            $lignesReservations[] = [
                'email' => $reservation->identifiantClient(),
                'parkingId' => $reservation->identifiantParking(),
                'startTimestamp' => $reservation->debut()->getTimestamp(),
                'endTimestamp' => $reservation->fin()->getTimestamp(),
            ];
        }
        $lignesStationnements = [];
        foreach ($this->stationnements->tous() as $stationnement) {
            $lignesStationnements[] = [
                'email' => $stationnement->identifiantClient(),
                'parkingId' => $stationnement->identifiantParking(),
                'entryTimestamp' => $stationnement->heureEntree()->getTimestamp(),
                'isOpen' => $stationnement->heureSortie() === null,
            ];
        }

        $sortie->transmettre(new ReponseRecapitulatifDemo(
            $lignesParkings,
            $lignesClients,
            $lignesReservations,
            $lignesStationnements
        ));
    }
}
