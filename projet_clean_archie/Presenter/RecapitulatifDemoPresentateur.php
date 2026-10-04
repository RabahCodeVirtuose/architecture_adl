<?php

final class RecapitulatifDemoPresentateur implements PortSortieRecapitulatifDemo
{
    public RecapitulatifDemoViewModel $modeleVue;

    public function transmettre(ReponseRecapitulatifDemo $reponse): void
    {
        $nomsParkings = [];
        foreach ($reponse->optionsParkings as $parking) {
            $nomsParkings[$parking['id']] = $parking['name'];
        }
        $recapitulatifReservations = [];
        foreach ($reponse->reservations as $reservation) {
            $recapitulatifReservations[] = [
                'email' => $reservation['email'],
                'parking' => $nomsParkings[$reservation['parkingId']] ?? $reservation['parkingId'],
                'start' => $this->formaterHorodatage($reservation['startTimestamp']),
                'end' => $this->formaterHorodatage($reservation['endTimestamp']),
            ];
        }
        $recapitulatifStationnements = [];
        foreach ($reponse->stationnements as $stationnement) {
            $recapitulatifStationnements[] = [
                'email' => $stationnement['email'],
                'parking' => $nomsParkings[$stationnement['parkingId']] ?? $stationnement['parkingId'],
                'entered' => $this->formaterHorodatage($stationnement['entryTimestamp']),
                'open' => $stationnement['isOpen'],
            ];
        }
        $this->modeleVue = new RecapitulatifDemoViewModel(
            $reponse->optionsParkings,
            $reponse->optionsClients,
            $recapitulatifReservations,
            $recapitulatifStationnements
        );
    }

    private function formaterHorodatage(int $horodatage): string
    {
        return (new DateTimeImmutable('@' . $horodatage))
            ->setTimezone(new DateTimeZone('Europe/Paris'))
            ->format('d/m/Y H:i');
    }
}
