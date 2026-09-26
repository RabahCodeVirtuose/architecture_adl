<?php

class AddReservationViewModel
{
	public float $price;
    public bool $success;

	public function __construct(float $price, bool $success)
	{
		$this->price = $price;
        $this->success = $success;
	}
}
