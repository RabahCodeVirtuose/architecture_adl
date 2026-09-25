<?php

// Point d'entrée unique pour charger toutes les classes du projet.
// (Le projet n'ayant pas de composer.json / autoload PSR-4, on charge
// explicitement chaque fichier, dans un ordre sûr.)

// Entités (namespace App\Domain\Entity)
require_once __DIR__ . '/Entity/Customer.php';
require_once __DIR__ . '/Entity/Parking.php';
require_once __DIR__ . '/Entity/ParkingSession.php';
require_once __DIR__ . '/Entity/Reservation.php';

// DTO
require_once __DIR__ . '/DTO/LocateParkingsRequest.php';

// Repository
require_once __DIR__ . '/Repository/Repository.php';

// UseCases
require_once __DIR__ . '/UseCase/LocateParkingUseCase.php';
require_once __DIR__ . '/UseCase/EnterParkingUseCase.php';
require_once __DIR__ . '/UseCase/AddReservationUseCase.php';

// ViewModel
require_once __DIR__ . '/VM/LocateParkingViewModel.php';

// Presenter
require_once __DIR__ . '/Presenter/LocateParkingPresenter.php';
