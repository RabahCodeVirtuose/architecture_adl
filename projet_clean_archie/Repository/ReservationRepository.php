<?php

interface IReservationRepository
{
    /** @return Reservation[] */
    public function findOverlappingByParkingId(string $parkingId, DateTimeImmutable $start, DateTimeImmutable $end): array;

    public function save(Reservation $reservation): void;
}

class ReservationRepository implements IReservationRepository
{
    private array $reservations = [];

    public function findOverlappingByParkingId(string $parkingId, DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        $overlapping = [];
        foreach ($this->reservations as $reservation) {
            if ($reservation->getParkingId() === $parkingId && $reservation->overlapsWith($start, $end)) {
                $overlapping[] = $reservation;
            }
        }

        return $overlapping;
    }

    public function save(Reservation $reservation): void
    {
        $this->reservations[] = $reservation;
    }
}
