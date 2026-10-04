<?php

final class LocaliserParkingsUseCase
{
    public function __construct(
        private readonly IRepositoryListeParkings $depotParkings,
        private readonly IRepositoryReservations $depotReservations,
        private readonly IRepositoryStationnements $depotStationnements,
        private readonly Horloge $horloge
    ) {}

    public function executer(RequeteLocaliserParkings $requete, PortSortieLocalisationParkings $sortie): void
    {
        $parkings = $this->depotParkings->listerParkings();
        $parkingsDansLeRayon = [];
        $instantCalcul = $this->horloge->maintenant();

        foreach ($parkings as $parking) {
            $distanceKm = $this->calculerDistanceKm(
                $requete->latitudeCentre,
                $requete->longitudeCentre,
                $parking->latitude(),
                $parking->longitude()
            );

            if ($distanceKm > $requete->rayonKm) {
                continue;
            }

            $instantane = $parking->instantaneDisponibiliteA(
                $instantCalcul,
                $this->depotReservations->trouverReservationsActives($parking->identifiant(), $instantCalcul),
                $this->depotStationnements->trouverStationnementsOuvertsParParking($parking->identifiant())
            );
            $horairesOuverture = $parking->horairesOuverture();
            $parkingsDansLeRayon[] = [
                'id' => $parking->identifiant(),
                'name' => $parking->nom(),
                'address' => $parking->adresse(),
                'addressIsFictional' => $parking->adresseEstFictive(),
                'latitude' => $parking->latitude(),
                'longitude' => $parking->longitude(),
                'totalSpots' => $parking->capaciteTotale(),
                ...$instantane,
                'alwaysOpen' => $horairesOuverture->estToujoursOuvert(),
                'weeklyWindows' => $horairesOuverture->plagesHebdomadaires(),
                'tariffTiers' => $parking->grilleTarifaire()->paliers(),
            ];
        }

        $reponse = new ReponseLocaliserParkings($parkingsDansLeRayon, $instantCalcul->getTimestamp());
        $sortie->transmettre($reponse);
    }

    private function calculerDistanceKm(
        float $latitudeDepart,
        float $longitudeDepart,
        float $latitudeArrivee,
        float $longitudeArrivee
    ): float {
        $rayonTerreKm = 6371.0;
        $ecartLatitude = deg2rad($latitudeArrivee - $latitudeDepart);
        $ecartLongitude = deg2rad($longitudeArrivee - $longitudeDepart);

        $sinDemiEcartLatitude = sin($ecartLatitude / 2);
        $sinDemiEcartLongitude = sin($ecartLongitude / 2);
        $valeurHaversine = $sinDemiEcartLatitude * $sinDemiEcartLatitude
            + cos(deg2rad($latitudeDepart)) * cos(deg2rad($latitudeArrivee))
            * $sinDemiEcartLongitude * $sinDemiEcartLongitude;
        $valeurHaversine = min(1.0, max(0.0, $valeurHaversine));

        $angleCentral = 2 * atan2(sqrt($valeurHaversine), sqrt(1 - $valeurHaversine));
        return $rayonTerreKm * $angleCentral;
    }
}
