<?php

class ParkingSession
{
    public function __construct(
        private readonly string $id,
        private readonly string $reservationId,
        private readonly string $customerId,
        private readonly string $parkingId,
        private readonly DateTimeImmutable $entryTime,
        private ?DateTimeImmutable $exitTime = null
    ) {}

    public function identifiant(): string
    {
        return $this->id;
    }

    public function identifiantClient(): string
    {
        return $this->customerId;
    }

    public function identifiantReservation(): string
    {
        return $this->reservationId;
    }

    public function identifiantParking(): string
    {
        return $this->parkingId;
    }

    public function heureEntree(): DateTimeImmutable
    {
        return $this->entryTime;
    }

    public function heureSortie(): ?DateTimeImmutable
    {
        return $this->exitTime;
    }
}

// Alias français ; l'ancien nom de classe reste nécessaire pour relire les sessions existantes.
class_alias(ParkingSession::class, 'Stationnement');
