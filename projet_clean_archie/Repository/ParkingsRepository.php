<?php

interface IGetAllParkingRepository
{
	public function getAllParking(): array;
}

class ParkingsRepository implements IGetAllParkingRepository
{
    /** @param Parking[] $parkings */
    public function __construct(private array $parkings)
    {
    }

    public function getAllParking(): array
    {
        return $this->parkings;
    }
}
