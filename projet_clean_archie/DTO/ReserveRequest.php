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
    public const SUCCESS = 'success';
    public const INVALID_DATES = 'invalid_dates';
    public const PARKING_NOT_FOUND = 'parking_not_found';
    public const CUSTOMER_NOT_FOUND = 'customer_not_found';
    public const CAPACITY_UNAVAILABLE = 'capacity_unavailable';

    public string $status;
    public bool $success;
    public float $price;

    public function __construct(string $status, float $price = 0.0)
    {
        $this->status = $status;
        $this->success = $status === self::SUCCESS;
        $this->price = $price;
    }
}
