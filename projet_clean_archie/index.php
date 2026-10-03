<?php

require_once __DIR__ . '/bootstrap.php';

$parkingRepository = new ParkingsRepository([
    new Parking('park_in_1', 0.05, 0.05, 50, 1.50),
    new Parking('park_in_2', -0.12, 0.00, 100, 2.00),
    new Parking('park_out_1', 0.50, 0.50, 30, 1.00),
]);
$request = new LocateParkingsRequest(0.0, 0.0, 20);
$presenter = new LocateParkingPresenter($parkingRepository);
$result = $presenter->execute($request);

$parkingByIdRepo = new GetParkingByIdRepository($parkingRepository);
$userByEmailRepo = new GetCustomerByEmailRepository([
    new Customer('test@gmail.com', 'test123', 'Pierre', 'Dupond'),
]);
$reservationRepo = new ReservationRepository();

$demoStart = new DateTimeImmutable('tomorrow 14:00', new DateTimeZone('Europe/Paris'));
$requestReserv = new ReserveSpotRequest('test@gmail.com', 'park_in_1', $demoStart, $demoStart->modify('+2 hours'));
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
    echo "<p style='color: green;'>" . htmlspecialchars($viewModelReserv->message, ENT_QUOTES, 'UTF-8')
        . " Prix total : " . $viewModelReserv->price . " €</p>";
} else {
    echo "<p style='color: red;'>" . htmlspecialchars($viewModelReserv->message, ENT_QUOTES, 'UTF-8') . "</p>";
}
