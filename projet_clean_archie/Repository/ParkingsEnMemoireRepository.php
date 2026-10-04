<?php

class ParkingsEnMemoireRepository implements IRepositoryListeParkings
{
    /** @param Parking[] $parkings */
    public function __construct(private array $parkings)
    {
    }

    public function listerParkings(): array
    {
        return $this->parkings;
    }

    /** @return Parking[] */
    public function tous(): array
    {
        return $this->parkings;
    }
}
