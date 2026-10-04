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
            throw new DomainException('La date de fin doit être postérieure à la date de début.');
        }
    }

    public function identifiant(): string
    {
        return $this->id;
    }

    public function identifiantClient(): string
    {
        return $this->customerId;
    }

    public function identifiantParking(): string
    {
        return $this->parkingId;
    }

    public function debut(): DateTimeImmutable
    {
        return $this->start;
    }

    public function fin(): DateTimeImmutable
    {
        return $this->end;
    }

    public function estActiveA(DateTimeImmutable $instant): bool
    {
        return $instant >= $this->start && $instant < $this->end;
    }

    public function chevauche(DateTimeImmutable $debutVerifie, DateTimeImmutable $finVerifiee): bool
    {
        return $this->start < $finVerifiee && $this->end > $debutVerifie;
    }
}
