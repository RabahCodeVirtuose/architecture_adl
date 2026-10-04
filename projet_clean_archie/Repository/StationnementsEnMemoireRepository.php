<?php

final class StationnementsEnMemoireRepository implements IRepositoryStationnements
{
    /** @param Stationnement[] $stationnements */
    public function __construct(private array $stationnements = []) {}

    public function trouverStationnementsOuvertsParParking(string $identifiantParking): array
    {
        $stationnementsOuverts = [];
        foreach ($this->stationnements as $stationnement) {
            if ($stationnement->identifiantParking() === $identifiantParking
                && $stationnement->heureSortie() === null) {
                $stationnementsOuverts[] = $stationnement;
            }
        }

        return $stationnementsOuverts;
    }
    public function trouverStationnementOuvertParClient(string $identifiantClient): ?Stationnement
    {
        foreach ($this->stationnements as $stationnement) {
            if ($stationnement->identifiantClient() === $identifiantClient
                && $stationnement->heureSortie() === null) {
                return $stationnement;
            }
        }
        return null;
    }
    public function trouverStationnementOuvertParReservation(string $identifiantReservation): ?Stationnement
    {
        foreach ($this->stationnements as $stationnement) {
            if ($stationnement->identifiantReservation() === $identifiantReservation
                && $stationnement->heureSortie() === null) {
                return $stationnement;
            }
        }
        return null;
    }
    public function enregistrer(Stationnement $stationnement): void
    {
        $this->stationnements[] = $stationnement;
    }

    /** @return Stationnement[] */
    public function tous(): array
    {
        return $this->stationnements;
    }
}
