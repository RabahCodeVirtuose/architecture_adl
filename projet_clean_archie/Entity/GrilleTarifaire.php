<?php

/** Tarifs en centimes appliqués par tranches de 15 minutes selon la durée réservée. */
final class ParkingTariff
{
    /** @param array<array{fromMinute:int, centsPerQuarterHour:int}> $tiers */
    public function __construct(private readonly array $tiers)
    {
        if ($tiers === []) {
            throw new InvalidArgumentException('La grille tarifaire ne peut pas être vide.');
        }
        if (!array_is_list($tiers)) {
            throw new InvalidArgumentException('Les paliers tarifaires doivent être fournis dans une liste ordonnée.');
        }

        $seuilPrecedent = -1;
        foreach ($tiers as $indice => $palier) {
            if (!is_array($palier)
                || !array_key_exists('fromMinute', $palier)
                || !array_key_exists('centsPerQuarterHour', $palier)
                || !is_int($palier['fromMinute'])
                || !is_int($palier['centsPerQuarterHour'])) {
                throw new InvalidArgumentException('Chaque palier doit contenir un seuil et un tarif entiers.');
            }

            $seuil = $palier['fromMinute'];
            $prixParQuartHeure = $palier['centsPerQuarterHour'];
            if ($seuil < 0) {
                throw new InvalidArgumentException('Le seuil d’un palier ne peut pas être négatif.');
            }
            if ($indice > 0 && $seuil <= $seuilPrecedent) {
                throw new InvalidArgumentException('Les seuils doivent être strictement croissants et sans doublon.');
            }
            if ($seuil % 15 !== 0) {
                throw new InvalidArgumentException('Chaque seuil doit être aligné sur une tranche de 15 minutes.');
            }
            if ($prixParQuartHeure < 0) {
                throw new InvalidArgumentException('Le prix par tranche ne peut pas être négatif.');
            }

            $seuilPrecedent = $seuil;
        }

        if ($tiers[0]['fromMinute'] !== 0) {
            throw new InvalidArgumentException('Le premier palier doit commencer à 0 minute.');
        }
    }

    /** @return array<array{fromMinute:int, centsPerQuarterHour:int}> */
    public function paliers(): array
    {
        return $this->tiers;
    }

    public function calculerPrixCentimes(DateTimeImmutable $debut, DateTimeImmutable $fin): int
    {
        $dureeSecondes = $fin->getTimestamp() - $debut->getTimestamp();
        if ($dureeSecondes <= 0) {
            throw new DomainException('La fin doit être postérieure au début.');
        }

        // L'ajout de 899 secondes arrondit toute tranche entamée au quart d'heure supérieur.
        $nombreQuarts = intdiv($dureeSecondes + 899, 900);
        $prixTotal = 0;
        for ($indiceQuart = 0; $indiceQuart < $nombreQuarts; $indiceQuart++) {
            $minuteDebutQuart = $indiceQuart * 15;
            $prixParQuartHeure = $this->tiers[0]['centsPerQuarterHour'];
            foreach ($this->tiers as $palier) {
                if ($minuteDebutQuart < $palier['fromMinute']) {
                    break;
                }
                $prixParQuartHeure = $palier['centsPerQuarterHour'];
            }
            $prixTotal += $prixParQuartHeure;
        }

        return $prixTotal;
    }
}

// Alias français ; l'ancien nom reste nécessaire pour les objets de session déjà enregistrés.
class_alias(ParkingTariff::class, 'GrilleTarifaire');
