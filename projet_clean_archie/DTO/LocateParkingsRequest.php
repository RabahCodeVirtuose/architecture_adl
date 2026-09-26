<?php

class LocateParkingsRequest
{
	public float $centerLat;
	public float $centerLong;
	public int $range;

	public function __construct($centerLat, $centerLong, $range)
	{
		$this->centerLat = $centerLat;
		$this->centerLong = $centerLong;
		$this->range = $range;
	}
}

class LocateParkingsResponse
{
	public array $listParkingsInRange;

	public function __construct($listParkingsInRange)
	{
		$this->listParkingsInRange = $listParkingsInRange;
	}
}
