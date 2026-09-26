<?php

interface IGetAllParkingRepository
{
	public function getAllParking() : array;

}

class ParkingsRepository implements IGetAllParkingRepository
{
    public function __construct()
	{

	}

	public function getAllParking() : array
	{
		// 1. Parking IN (Dans les 20 km)
		// Coordonnées : 0.05, 0.05 (Environ 7.8 km du centre)
		$parkingIn1 = new Parking(
			'park_in_1', 
			0.05, 
			0.05, 
			50, 
			1.50
		);

		// 2. Parking IN (Dans les 20 km)
		// Coordonnées : -0.12, 0.00 (Environ 13.3 km du centre)
		$parkingIn2 = new Parking(
			'park_in_2', 
			-0.12, 
			0.00, 
			100, 
			2.00
		);

		// 3. Parking OUT (Hors des 20 km)
		// Coordonnées : 0.50, 0.50 (Environ 78 km du centre)
		$parkingOut1 = new Parking(
			'park_out_1', 
			0.50, 
			0.50, 
			30, 
			1.00
		);

		$listParkings = [$parkingIn1, $parkingIn2, $parkingOut1];
		return $listParkings;
	}
}
