<?php

class LocateParkingUseCase
{
	private IGetAllParkingRepository $repo;

	public function __construct($repo)
	{
		$this->repo = $repo;
	}

	public function execute(LocateParkingsRequest $request) : LocateParkingsResponse
	{
		$listParkings = $this->repo->getAllParking();
        $listParkingsInRange = [];

        foreach ($listParkings as $parking) {
            $distance = $this->calculateDistance(
                $request->centerLat,
                $request->centerLong,
                $parking->getLatitude(),
                $parking->getLongitude()
            );

            if ($distance <= $request->range) {
                $listParkingsInRange[] = $parking;
            }
        }

        return new LocateParkingsResponse($listParkingsInRange);
	}

	private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
