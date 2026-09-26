<?php

interface IGetParkingByIdRepository
{
    public function getParkingById($id) : Parking;
}

class GetParkingByIdRepository implements IGetParkingByIdRepository
{
    public function getParkingById($id): Parking
    {
        $parking = new Parking('park_in_1', 0.05, 0.05, 50, 1.50);
        return $parking;
    }
}

class GetParkingByIdRepositoryFull implements IGetParkingByIdRepository
{
    public function getParkingById($id): Parking
    {
        $parking = new Parking('park_in_1', 0.05, 0.05, 0, 1.50);
        return $parking;
    }
}
