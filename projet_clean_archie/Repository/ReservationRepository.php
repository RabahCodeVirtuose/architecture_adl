<?php

interface IReservationRepository
{
    public function countActiveReservations(string $parkingId, DateTimeImmutable $start, DateTimeImmutable $end): int;

    public function save(Reservation $reservation): void;
}

class ReservationRepository implements IReservationRepository
{
    private array $reservations = [];

    public function countActiveReservations(string $parkingId, DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        $count = 0;
        foreach ($this->reservations as $reservation) {
            if ($reservation->getParkingId() === $parkingId) {
                if ($reservation->overlapsWith($start, $end)) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function save(Reservation $reservation): void
    {
        $this->reservations[] = $reservation;
    }
}
