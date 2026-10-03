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

    /** @param Reservation[] $reservations */
    public function maximumSimultaneousReservations(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        array $reservations
    ): int
    {
        if ($start >= $end) {
            throw new DomainException('La date de fin doit être postérieure à la date de début.');
        }

        $events = [];
        foreach ($reservations as $reservation) {
            if ($reservation->getParkingId() !== $this->id || !$reservation->overlapsWith($start, $end)) {
                continue;
            }

            $occupiedFrom = $reservation->getStart() > $start ? $reservation->getStart() : $start;
            $occupiedUntil = $reservation->getEnd() < $end ? $reservation->getEnd() : $end;
            $events[] = ['time' => $occupiedFrom, 'change' => 1];
            $events[] = ['time' => $occupiedUntil, 'change' => -1];
        }

        // À heure égale, une fin libère sa place avant un nouveau début.
        usort($events, static function (array $first, array $second): int {
            return ($first['time'] <=> $second['time']) ?: ($first['change'] <=> $second['change']);
        });

        $currentOccupancy = 0;
        $maximumOccupancy = 0;
        foreach ($events as $event) {
            $currentOccupancy += $event['change'];
            $maximumOccupancy = max($maximumOccupancy, $currentOccupancy);
        }

        return $maximumOccupancy;
    }

    public function canAccept(int $maximumSimultaneousReservations): bool
    {
        return $maximumSimultaneousReservations < $this->totalSpots;
    }
}
