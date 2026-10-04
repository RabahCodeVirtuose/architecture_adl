<?php

class Parking
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $address,
        private readonly bool $addressIsFictional,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly int $totalSpots,
        private readonly HorairesOuverture $openingHours,
        private readonly GrilleTarifaire $tariff
    ) {}

    public function identifiant(): string
    {
        return $this->id;
    }

    public function nom(): string
    {
        return $this->name;
    }

    public function adresse(): string
    {
        return $this->address;
    }

    public function adresseEstFictive(): bool
    {
        return $this->addressIsFictional;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function capaciteTotale(): int
    {
        return $this->totalSpots;
    }

    public function horairesOuverture(): HorairesOuverture
    {
        return $this->openingHours;
    }

    public function grilleTarifaire(): GrilleTarifaire
    {
        return $this->tariff;
    }

    public function calculerPrixCentimes(DateTimeImmutable $debut, DateTimeImmutable $fin): int
    {
        return $this->tariff->calculerPrixCentimes($debut, $fin);
    }

    public function estOuvertPendant(DateTimeImmutable $debut, DateTimeImmutable $fin): bool
    {
        return $this->openingHours->couvre($debut, $fin);
    }

    public function estOuvertA(DateTimeImmutable $instant): bool
    {
        return $this->openingHours->estOuvertA($instant);
    }

    /** @param Stationnement[] $stationnementsOuverts */
    public function compterVehiculesPresentsA(DateTimeImmutable $instant, array $stationnementsOuverts): int
    {
        $identifiantsPresents = [];
        foreach ($stationnementsOuverts as $stationnement) {
            if ($stationnement->identifiantParking() === $this->id
                && $stationnement->heureSortie() === null
                && $stationnement->heureEntree() <= $instant) {
                $identifiantsPresents[$stationnement->identifiant()] = true;
            }
        }

        return count($identifiantsPresents);
    }

    /**
     * Compte les réservations actives et les stationnements ouverts non déjà couverts par leur réservation.
     * @param Reservation[] $reservationsActives
     * @param Stationnement[] $stationnementsOuverts
     */
    public function compterPlacesIndisponiblesA(
        DateTimeImmutable $instant,
        array $reservationsActives,
        array $stationnementsOuverts
    ): int {
        $occupationsBloquantes = [];
        foreach ($reservationsActives as $reservation) {
            if ($reservation->identifiantParking() === $this->id && $reservation->estActiveA($instant)) {
                $occupationsBloquantes[$reservation->identifiant()] = true;
            }
        }

        foreach ($stationnementsOuverts as $stationnement) {
            if ($stationnement->identifiantParking() !== $this->id
                || $stationnement->heureSortie() !== null
                || $stationnement->heureEntree() > $instant) {
                continue;
            }

            // Une entrée liée à une réservation active ne bloque qu'une seule place.
            $identifiantReservation = $stationnement->identifiantReservation();
            if (!isset($occupationsBloquantes[$identifiantReservation])) {
                $occupationsBloquantes[$identifiantReservation] = true;
            }
        }

        return count($occupationsBloquantes);
    }

    /** @param Stationnement[] $stationnementsOuverts */
    public function peutAccepterEntreePhysiqueA(DateTimeImmutable $instant, array $stationnementsOuverts): bool
    {
        return $this->compterVehiculesPresentsA($instant, $stationnementsOuverts) < $this->totalSpots;
    }

    /** @param Reservation[] $reservationsActives @param Stationnement[] $stationnementsOuverts */
    public function instantaneDisponibiliteA(
        DateTimeImmutable $instant,
        array $reservationsActives,
        array $stationnementsOuverts
    ): array {
        $vehiculesPresents = $this->compterVehiculesPresentsA($instant, $stationnementsOuverts);
        $placesIndisponibles = $this->compterPlacesIndisponiblesA($instant, $reservationsActives, $stationnementsOuverts);
        $placesDisponibles = max(0, $this->totalSpots - $placesIndisponibles);
        $estOuvert = $this->estOuvertA($instant);

        if (!$estOuvert) {
            $statut = 'closed';
        } elseif ($placesDisponibles === 0) {
            $statut = 'full';
        } elseif ($placesIndisponibles === 0) {
            $statut = 'available';
        } else {
            $statut = 'partial';
        }

        return [
            'vehiclesPresent' => $vehiculesPresents,
            'unavailableSpots' => $placesIndisponibles,
            'availableSpots' => $placesDisponibles,
            'isOpen' => $estOuvert,
            'status' => $statut,
        ];
    }

    /**
     * Calcule le maximum de places occupées pendant toute la période demandée.
     * Un stationnement ouvert prolonge sa réservation ; après son expiration, il continue de bloquer une place.
     * @param Reservation[] $reservations
     * @param Stationnement[] $stationnements
     */
    public function occupationMaximaleSimultanee(
        DateTimeImmutable $debut,
        DateTimeImmutable $fin,
        array $reservations,
        array $stationnements = []
    ): int {
        if ($debut >= $fin) {
            throw new DomainException('La date de fin doit être postérieure à la date de début.');
        }

        $stationnementsParReservation = [];
        foreach ($stationnements as $stationnement) {
            if ($stationnement->identifiantParking() === $this->id && $stationnement->heureSortie() === null) {
                $stationnementsParReservation[$stationnement->identifiantReservation()] = $stationnement;
            }
        }

        $evenements = [];
        $reservationsComptees = [];
        foreach ($reservations as $reservation) {
            if ($reservation->identifiantParking() !== $this->id) {
                continue;
            }

            $stationnement = $stationnementsParReservation[$reservation->identifiant()] ?? null;
            $finEffective = $stationnement !== null ? $fin : $reservation->fin();
            if ($reservation->debut() >= $fin || $finEffective <= $debut) {
                continue;
            }

            $debutOccupation = $reservation->debut() > $debut ? $reservation->debut() : $debut;
            $finOccupation = $finEffective < $fin ? $finEffective : $fin;
            $evenements[] = ['time' => $debutOccupation, 'change' => 1];
            $evenements[] = ['time' => $finOccupation, 'change' => -1];
            $reservationsComptees[$reservation->identifiant()] = true;
        }

        foreach ($stationnements as $stationnement) {
            if ($stationnement->identifiantParking() !== $this->id
                || $stationnement->heureSortie() !== null
                || isset($reservationsComptees[$stationnement->identifiantReservation()])) {
                continue;
            }

            $debutOccupation = $stationnement->heureEntree() > $debut ? $stationnement->heureEntree() : $debut;
            if ($debutOccupation < $fin) {
                $evenements[] = ['time' => $debutOccupation, 'change' => 1];
                $evenements[] = ['time' => $fin, 'change' => -1];
            }
        }

        // À la même heure, on traite les fins avant les débuts : une place se libère à l'heure exacte de fin.
        usort($evenements, static function (array $premier, array $deuxieme): int {
            return ($premier['time'] <=> $deuxieme['time']) ?: ($premier['change'] <=> $deuxieme['change']);
        });

        $occupationActuelle = 0;
        $occupationMaximale = 0;
        foreach ($evenements as $evenement) {
            $occupationActuelle += $evenement['change'];
            $occupationMaximale = max($occupationMaximale, $occupationActuelle);
        }

        return $occupationMaximale;
    }

    public function peutAccepterReservation(int $occupationMaximale): bool
    {
        return $occupationMaximale < $this->totalSpots;
    }
}
