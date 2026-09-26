<?php

class Parking
{
    public function __construct(
        private readonly string $id,
        private readonly float $latitude,
        private readonly float $longitude,
        private readonly int $totalSpots,
        private readonly float $pricePer15Minutes
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getTotalSpots(): int
    {
        return $this->totalSpots;
    }

    public function getPricePer15Minutes(): float
    {
        return $this->pricePer15Minutes;
    }

    public function calculatePrice(DateTimeImmutable $start, DateTimeImmutable $end): float
    {
        $durationInSeconds = $end->getTimestamp() - $start->getTimestamp();
        $durationInMinutes = $durationInSeconds / 60;

        $blocksOf15Mins = ceil($durationInMinutes / 15);

        return max(0, $blocksOf15Mins) * $this->pricePer15Minutes;
    }

    public function canAccept(int $activeReservationsCount): bool
    {
        return $activeReservationsCount < $this->totalSpots;
    }
}
