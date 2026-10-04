<?php

/** Données fictives créées une seule fois à l'initialisation de la session de démonstration. */
final class FixturesDemo
{
    public static function creer(): array
    {
        $parkings = [
            new Parking('park_orleans_centre', 'Orléans Centre', 'Secteur place du Martroi, Orléans', true,
                47.9029, 1.9086, 3, HorairesOuverture::toujoursOuvert(), new GrilleTarifaire([
                ['fromMinute' => 0, 'centsPerQuarterHour' => 150],
            ])),
            new Parking('park_orleans_gare', 'Orléans Gare', 'Secteur gare d’Orléans, Orléans', true,
                47.9112, 1.9045, 2, new HorairesOuverture(false, [
                1 => [['start' => '08:00', 'end' => '18:00', 'endDayOffset' => 0]],
                2 => [['start' => '08:00', 'end' => '18:00', 'endDayOffset' => 0]],
                3 => [['start' => '08:00', 'end' => '18:00', 'endDayOffset' => 0]],
                4 => [['start' => '08:00', 'end' => '18:00', 'endDayOffset' => 0]],
                5 => [['start' => '18:00', 'end' => '08:00', 'endDayOffset' => 3]],
                6 => [['start' => '08:00', 'end' => '12:00', 'endDayOffset' => 0]],
            ]), new GrilleTarifaire([
                ['fromMinute' => 0, 'centsPerQuarterHour' => 200],
                ['fromMinute' => 60, 'centsPerQuarterHour' => 100],
            ])),
            new Parking('park_loiret_exterieur', 'Parking sud du Loiret', 'Secteur sud du Loiret', true,
                47.55, 2.05, 4, HorairesOuverture::toujoursOuvert(), new GrilleTarifaire([
                ['fromMinute' => 0, 'centsPerQuarterHour' => 125],
            ])),
        ];
        $clients = [
            new Client('pierre@example.test', 'test123', 'Pierre', 'Dupond'),
            new Client('camille@example.test', 'test123', 'Camille', 'Martin'),
        ];
        return ['parkings' => $parkings, 'customers' => $clients, 'reservations' => [], 'sessions' => []];
    }
}
