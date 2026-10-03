<?php

interface IGetParkingByIdRepository
{
    public function getParkingById(string $id): ?Parking;
}

class GetParkingByIdRepository implements IGetParkingByIdRepository
{
    public function __construct(private IGetAllParkingRepository $parkings)
    {
    }

    public function getParkingById(string $id): ?Parking
    {
        foreach ($this->parkings->getAllParking() as $parking) {
            if ($parking->getId() === $id) {
                return $parking;
            }
        }

        return null;
    }
}
