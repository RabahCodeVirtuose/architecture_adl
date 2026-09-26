<?php

// Point d'entrée unique pour charger toutes les classes du projet.
// (Le projet n'ayant pas de composer.json / autoload PSR-4, on charge
// explicitement chaque fichier, dans un ordre sûr :
// Entity -> DTO -> Repository -> UseCase -> VM -> Presenter.)

// Entités (namespace App\Domain\Entity)
require_once __DIR__ . '/Entity/Customer.php';
require_once __DIR__ . '/Entity/Parking.php';
require_once __DIR__ . '/Entity/ParkingSession.php';
require_once __DIR__ . '/Entity/Reservation.php';

// DTO (namespace App\DTO)
require_once __DIR__ . '/DTO/LocateParkingsRequest.php';
require_once __DIR__ . '/DTO/ReserveRequest.php';

// Repository (namespace App\Repository)
require_once __DIR__ . '/Repository/CustomerIdRepository.php';
require_once __DIR__ . '/Repository/ParkingIdRepository.php';
require_once __DIR__ . '/Repository/ParkingsRepository.php';
require_once __DIR__ . '/Repository/ReservationRepository.php';

// UseCases (namespace App\UseCase)
require_once __DIR__ . '/UseCase/LocateParkingUseCase.php';
require_once __DIR__ . '/UseCase/EnterParkingUseCase.php'; // toujours vide - work in progress, non modifié
require_once __DIR__ . '/UseCase/AddReservationUseCase.php';

// ViewModel (namespace App\VM)
require_once __DIR__ . '/VM/LocateParkingViewModel.php';
require_once __DIR__ . '/VM/AddReservationViewModel.php';

// Presenter (namespace App\Presenter)
require_once __DIR__ . '/Presenter/LocateParkingPresenter.php';
require_once __DIR__ . '/Presenter/ReserverPresenter.php';
