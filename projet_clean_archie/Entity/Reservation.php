<?php

class Reservation
{
    public function __construct(
        private readonly string $id,
        private readonly string $customerId,
        private readonly string $parkingId,
        private readonly DateTimeImmutable $start,
        private readonly DateTimeImmutable $end
    ) {
        if ($this->start >= $this->end) {
            throw new DomainException("La date de fin doit être postérieure à la date de début.");
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getParkingId(): string
    {
        return $this->parkingId;
    }

    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    public function isActiveAt(DateTimeImmutable $time): bool
    {
        return $time >= $this->start && $time <= $this->end;
    }

    public function overlapsWith(DateTimeImmutable $checkStart, DateTimeImmutable $checkEnd): bool
    {
        return $this->start < $checkEnd && $this->end > $checkStart;
    }
}
