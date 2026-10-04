<?php

// Chargement explicite adapté à ce POC sans Composer.
foreach ([
    'Entity/Client.php', 'Entity/HorairesOuverture.php', 'Entity/GrilleTarifaire.php',
    'Entity/Parking.php', 'Entity/Stationnement.php', 'Entity/Reservation.php',
    'DTO/RequeteLocaliserParkings.php', 'DTO/RequeteReserverPlace.php', 'DTO/RequeteEntrerParking.php', 'DTO/RequeteRecapitulatifDemo.php',
    'Application/Contrats.php',
    'Repository/ClientsParCourrielRepository.php', 'Repository/ParkingsEnMemoireRepository.php',
    'Repository/ParkingParIdRepository.php', 'Repository/ReservationsEnMemoireRepository.php',
    'Repository/StationnementsEnMemoireRepository.php',
    'Infrastructure/HorlogeSysteme.php', 'Infrastructure/FixturesDemo.php',
    'UseCase/LocaliserParkingsUseCase.php', 'UseCase/ReserverPlaceUseCase.php',
    'UseCase/EntrerParkingUseCase.php', 'UseCase/LireRecapitulatifDemoUseCase.php',
    'VM/LocaliserParkingsViewModel.php', 'VM/ReserverPlaceViewModel.php', 'VM/EntreeParkingViewModel.php', 'VM/RecapitulatifDemoViewModel.php',
    'Presenter/LocaliserParkingsPresentateur.php', 'Presenter/ReserverPlacePresentateur.php',
    'Presenter/EntrerParkingPresentateur.php', 'Presenter/RecapitulatifDemoPresentateur.php',
] as $cheminFichier) {
    require_once __DIR__ . '/' . $cheminFichier;
}
