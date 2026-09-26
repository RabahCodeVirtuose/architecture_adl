<?php

require_once __DIR__ . '/bootstrap.php';

$parkingRepository = new ParkingsRepository();
$request = new LocateParkingsRequest(0.0, 0.0, 20);
$presenter = new LocateParkingPresenter($parkingRepository);
$result = $presenter->execute($request);

$parkingByIdRepo = new GetParkingByIdRepository();
$userByEmailRepo = new GetCustomerByEmailRepository();
$reservationRepo = new ReservationRepository();

$requestReserv = new ReserveSpotRequest("test@gmail.com", "parking_01", new DateTimeImmutable("2026-09-26 14:00:00"), new DateTimeImmutable("2026-09-26 16:00:00"));
$presenterReserv = new ReserverPresenter($userByEmailRepo, $parkingByIdRepo, $reservationRepo);
$viewModelReserv = $presenterReserv->execute($requestReserv);

// send result to view
echo "<h1>Parkings dans le rayon demandé</h1>";
echo "<ul>";
foreach ($result->listParkings as $parking) {
    echo "<li>" . htmlspecialchars($parking->getId())
        . " (lat: " . $parking->getLatitude()
        . ", long: " . $parking->getLongitude() . ")</li>";
}
echo "</ul>";

echo "<h1>Résultat de la réservation</h1>";
if ($viewModelReserv->success) {
    echo "<p style='color: green;'>Réservation confirmée ! Prix total : " . $viewModelReserv->price . " €</p>";
} else {
    echo "<p style='color: red;'>Échec de la réservation : le parking est complet pour cette période.</p>";
}
