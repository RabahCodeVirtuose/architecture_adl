<?php

class ParkingSession
{
    public function __construct(
        private readonly string $id,
        private readonly string $customerId,
        private readonly string $parkingId,
        private readonly DateTimeImmutable $entryTime,
        private ?DateTimeImmutable $exitTime = null
    ) {}

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
    public function getEntryTime(): DateTimeImmutable
    {
        return $this->entryTime;
    }

    public function getExitTime(): ?DateTimeImmutable
    {
        return $this->exitTime;
    }
}
