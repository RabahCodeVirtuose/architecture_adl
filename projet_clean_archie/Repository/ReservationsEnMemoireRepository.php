<?php

class ReservationsEnMemoireRepository implements IRepositoryReservations
{
    /** @param Reservation[] $reservations */
    public function __construct(private array $reservations = []) {}

    public function trouverReservationsEnConflit(string $identifiantParking, DateTimeImmutable $debut, DateTimeImmutable $fin): array
    {
        $reservationsEnConflit = [];
        foreach ($this->reservations as $reservation) {
            if ($reservation->identifiantParking() === $identifiantParking
                && $reservation->chevauche($debut, $fin)) {
                $reservationsEnConflit[] = $reservation;
            }
        }

        return $reservationsEnConflit;
    }

    public function enregistrer(Reservation $reservation): void
    {
        $this->reservations[] = $reservation;
    }

    public function trouverReservationsClientParking(string $identifiantClient, string $identifiantParking): array
    {
        $reservationsTrouvees = [];
        foreach ($this->reservations as $reservation) {
            if ($reservation->identifiantClient() === $identifiantClient
                && $reservation->identifiantParking() === $identifiantParking) {
                $reservationsTrouvees[] = $reservation;
            }
        }

        return $reservationsTrouvees;
    }

    public function trouverReservationsActives(string $identifiantParking, DateTimeImmutable $instant): array
    {
        $reservationsActives = [];
        foreach ($this->reservations as $reservation) {
            if ($reservation->identifiantParking() === $identifiantParking && $reservation->estActiveA($instant)) {
                $reservationsActives[] = $reservation;
            }
        }

        return $reservationsActives;
    }

    /** @return Reservation[] */
    public function tous(): array
    {
        return $this->reservations;
    }
}
