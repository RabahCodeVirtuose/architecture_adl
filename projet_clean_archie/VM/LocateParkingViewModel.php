<?php 

class LocateParkingViewModel
{
	public array $listParkings;

	public function __construct($listParkings)
	{
		$this->listParkings = $listParkings;
	}
}