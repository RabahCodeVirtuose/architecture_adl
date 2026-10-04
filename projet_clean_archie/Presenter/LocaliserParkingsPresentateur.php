<?php

class LocaliserParkingsPresentateur implements PortSortieLocalisationParkings
{
    public LocaliserParkingsViewModel $modeleVue;

    public function transmettre(ReponseLocaliserParkings $reponse): void
    {
        $lignes = [];
        foreach ($reponse->parkingsDansLeRayon as $parking) {
            $lignes[] = [
                ...$parking,
                'addressLabel' => $parking['address'] . ($parking['addressIsFictional'] ? ' (adresse indicative fictive)' : ''),
                'statusLabel' => $this->libelleStatut($parking['status']),
                'statusClass' => $parking['status'],
                'openingHoursLabel' => $this->horairesAffiches($parking['alwaysOpen'], $parking['weeklyWindows']),
                'tariffLabel' => $this->tarifAffiche($parking['tariffTiers']),
            ];
        }
        $heureCalcul = (new DateTimeImmutable('@' . $reponse->horodatageCalcul))
            ->setTimezone(new DateTimeZone('Europe/Paris'))
            ->format('H:i:s');
        $this->modeleVue = new LocaliserParkingsViewModel($lignes, $heureCalcul);
    }

    private function libelleStatut(string $statut): string
    {
        return match ($statut) {
            'available' => 'Disponible',
            'partial' => 'Partiellement occupé ou réservé',
            'full' => 'Complet',
            'closed' => 'Fermé',
            default => 'État inconnu',
        };
    }

    private function horairesAffiches(bool $toujoursOuvert, array $plagesHebdomadaires): string
    {
        if ($toujoursOuvert) {
            return 'Ouvert 24 h/24 et 7 j/7';
        }
        $jours = [1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Jeu', 5 => 'Ven', 6 => 'Sam', 7 => 'Dim'];
        $elements = [];
        foreach ($plagesHebdomadaires as $jourSemaine => $plages) {
            foreach ($plages as $plage) {
                $plageAffichee = $plage['start'] . '–' . $plage['end'];
                if ($plage['endDayOffset'] > 0) {
                    $plageAffichee .= ' (+' . $plage['endDayOffset'] . ' j)';
                }
                $nomJour = $jours[$jourSemaine] ?? 'Jour ' . $jourSemaine;
                $elements[] = $nomJour . ' ' . $plageAffichee;
            }
        }
        if ($elements === []) {
            return 'Fermé toute la semaine';
        }

        return implode(' · ', $elements);
    }

    private function tarifAffiche(array $paliers): string
    {
        $elements = [];
        foreach ($paliers as $palier) {
            $prixAffiche = number_format($palier['centsPerQuarterHour'] / 100, 2, ',', ' ') . ' € / 15 min';
            if ($palier['fromMinute'] === 0) {
                $debutPalier = 'Dès le début';
            } else {
                $debutPalier = 'Dès ' . $palier['fromMinute'] . ' min';
            }
            $elements[] = $debutPalier . ' : ' . $prixAffiche;
        }
        return implode(' · ', $elements);
    }
}
