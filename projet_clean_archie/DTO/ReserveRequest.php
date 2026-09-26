<?php

class ReserveSpotRequest
{
    public string $clientEmail;
    public string $parkingId;
    public DateTimeImmutable $start;
    public DateTimeImmutable $end;

    public function __construct($clientEmail, $parkingId, $start, $end)
    {
        $this->clientEmail = $clientEmail;
        $this->parkingId = $parkingId;
        $this->start = $start;
        $this->end = $end;
    }
}

class ReserveSpotResponse
{
    public bool $success;
    public float $price;

    public function __construct($success, $price)
    {
        $this->success = $success;
        $this->price = $price;
    }
}
