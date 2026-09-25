<?php

require_once __DIR__ . '/bootstrap.php';

$parkingRepository = new ParkingsRepository();
$request = new LocateParkingsRequest(0.0, 0.0, 20);
$presenter = new LocateParkingPresenter($parkingRepository);
$result = $presenter->execute($request);

// send result to view
echo "<h1>Parkings dans le rayon demandé</h1>";
echo "<ul>";
foreach ($result->listParkings as $parking) {
    echo "<li>" . htmlspecialchars($parking->getId())
        . " (lat: " . $parking->getLatitude()
        . ", long: " . $parking->getLongitude() . ")</li>";
}
echo "</ul>";
