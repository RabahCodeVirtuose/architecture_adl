<?php

class AddReservationViewModel
{
	public float $price;
    public bool $success;
    public string $message;

	public function __construct(float $price, bool $success, string $message)
	{
		$this->price = $price;
        $this->success = $success;
        $this->message = $message;
	}
}
