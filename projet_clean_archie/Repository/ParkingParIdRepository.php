<?php

class ParkingParIdRepository implements IRepositoryParking
{
    public function __construct(private IRepositoryListeParkings $parkings)
    {
    }

    public function trouverParkingParId(string $identifiant): ?Parking
    {
        foreach ($this->parkings->listerParkings() as $parking) {
            if ($parking->identifiant() === $identifiant) {
                return $parking;
            }
        }

        return null;
    }
}
