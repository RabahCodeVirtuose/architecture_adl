<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';
date_default_timezone_set('Europe/Paris');
session_name('PARKINGPOC');
session_start();

function echapperHtml(string $valeur): string
{
    return htmlspecialchars($valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formaterDatePourChamp(DateTimeImmutable $date): string
{
    return $date->setTimezone(new DateTimeZone('Europe/Paris'))->format('Y-m-d\TH:i');
}

function lireDateSaisieLocale(mixed $valeur): ?DateTimeImmutable
{
    if (!is_string($valeur) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $valeur)) {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $valeur, new DateTimeZone('Europe/Paris'));
    $erreurs = DateTimeImmutable::getLastErrors();
    $dateValide = $date !== false
        && ($erreurs === false || ($erreurs['warning_count'] === 0 && $erreurs['error_count'] === 0))
        && $date->format('Y-m-d\TH:i') === $valeur;

    return $dateValide ? $date : null;
}

function validerIdentifiant(mixed $valeur): ?string
{
    if (!is_string($valeur) || preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $valeur) !== 1) {
        return null;
    }

    return $valeur;
}

function redirigerApresPost(): never
{
    $cheminScript = $_SERVER['SCRIPT_NAME'] ?? 'index.php';
    header('Location: ' . $cheminScript, true, 303);
    exit;
}

if (!isset($_SESSION['parking_poc_csrf'])) {
    $_SESSION['parking_poc_csrf'] = bin2hex(random_bytes(32));
}
if (!isset($_SESSION['parking_poc_data']) || !is_array($_SESSION['parking_poc_data'])) {
    $_SESSION['parking_poc_data'] = FixturesDemo::creer();
}
$etatSession = &$_SESSION['parking_poc_data'];

// Met à jour l'ancien parking éloigné dans les sessions déjà initialisées.
if (!isset($_SESSION['parking_poc_parking_saint_marceau_v1'])) {
    $parkingsDeReference = FixturesDemo::creer()['parkings'];
    foreach ($etatSession['parkings'] ?? [] as $indice => $parking) {
        if (!$parking instanceof Parking
            || $parking->identifiant() !== 'park_loiret_exterieur'
            || $parking->nom() !== 'Parking sud du Loiret') {
            continue;
        }

        foreach ($parkingsDeReference as $parkingDeReference) {
            if ($parkingDeReference->identifiant() === 'park_loiret_exterieur') {
                $etatSession['parkings'][$indice] = $parkingDeReference;
                break;
            }
        }
    }
    $_SESSION['parking_poc_parking_saint_marceau_v1'] = true;
}

// Retire seulement les anciennes réservations de démonstration, sans toucher aux réservations soumises.
$anciensIdentifiantsFixtures = ['demo-active-entry', 'demo-successive-a', 'demo-successive-b'];
if (!isset($_SESSION['parking_poc_fixture_cleanup_v1'])) {
    $reservationsConservees = [];
    foreach ($etatSession['reservations'] ?? [] as $reservation) {
        if (!$reservation instanceof Reservation
            || !in_array($reservation->identifiant(), $anciensIdentifiantsFixtures, true)) {
            $reservationsConservees[] = $reservation;
        }
    }
    $etatSession['reservations'] = $reservationsConservees;

    $stationnementsConserves = [];
    foreach ($etatSession['sessions'] ?? [] as $stationnement) {
        if (!$stationnement instanceof Stationnement
            || !in_array($stationnement->identifiantReservation(), $anciensIdentifiantsFixtures, true)) {
            $stationnementsConserves[] = $stationnement;
        }
    }
    $etatSession['sessions'] = $stationnementsConserves;
    $_SESSION['parking_poc_fixture_cleanup_v1'] = true;
}

$depotParkings = new ParkingsEnMemoireRepository($etatSession['parkings'] ?? []);
$depotParkingParId = new ParkingParIdRepository($depotParkings);
$depotClients = new ClientsParCourrielRepository($etatSession['customers'] ?? []);
$depotReservations = new ReservationsEnMemoireRepository($etatSession['reservations'] ?? []);
$depotStationnements = new StationnementsEnMemoireRepository($etatSession['sessions'] ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jetonSoumis = $_POST['csrf'] ?? null;
    if (!is_string($jetonSoumis) || !hash_equals($_SESSION['parking_poc_csrf'], $jetonSoumis)) {
        http_response_code(400);
        exit('Formulaire expiré ou invalide. Rechargez la page.');
    }

    $_SESSION['parking_poc_csrf'] = bin2hex(random_bytes(32));
    $action = $_POST['action'] ?? '';

    if ($action === 'reset') {
        $etatSession = FixturesDemo::creer();
        $depotParkings = new ParkingsEnMemoireRepository($etatSession['parkings']);
        $depotParkingParId = new ParkingParIdRepository($depotParkings);
        $depotClients = new ClientsParCourrielRepository($etatSession['customers']);
        $depotReservations = new ReservationsEnMemoireRepository($etatSession['reservations']);
        $depotStationnements = new StationnementsEnMemoireRepository($etatSession['sessions']);
        $_SESSION['parking_poc_flash'] = ['success', 'Données de démonstration réinitialisées pour cette session.'];
    } elseif ($action === 'reserve') {
        $courriel = $_POST['client_email'] ?? null;
        $identifiantParking = validerIdentifiant($_POST['parking_id'] ?? null);
        $debut = lireDateSaisieLocale($_POST['start'] ?? null);
        $fin = lireDateSaisieLocale($_POST['end'] ?? null);

        if (!is_string($courriel) || filter_var($courriel, FILTER_VALIDATE_EMAIL) === false
            || $identifiantParking === null || $debut === null || $fin === null) {
            $_SESSION['parking_poc_flash'] = [
                'error',
                'Données invalides : vérifiez le client, le parking et les dates (Europe/Paris).',
            ];
        } else {
            $presentateur = new ReserverPlacePresentateur();
            $useCase = new ReserverPlaceUseCase(
                $depotClients,
                $depotParkingParId,
                $depotReservations,
                $depotStationnements
            );

            try {
                $useCase->executer(
                    new RequeteReserverPlace($courriel, $identifiantParking, $debut, $fin),
                    $presentateur
                );
                $modeleVue = $presentateur->modeleVue;
                $typeMessage = $modeleVue->reussite ? 'success' : 'error';
                $message = $modeleVue->message;
                if ($modeleVue->reussite) {
                    $message .= ' Prix : ' . $modeleVue->prixAffiche;
                }
                $_SESSION['parking_poc_flash'] = [$typeMessage, $message];
            } catch (Throwable $erreurTechnique) {
                error_log((string) $erreurTechnique);
                $_SESSION['parking_poc_flash'] = [
                    'error',
                    'Erreur technique pendant la réservation. Consultez le journal PHP.',
                ];
            }
        }
    } elseif ($action === 'enter') {
        $courriel = $_POST['client_email'] ?? null;
        $identifiantParking = validerIdentifiant($_POST['parking_id'] ?? null);

        if (!is_string($courriel) || filter_var($courriel, FILTER_VALIDATE_EMAIL) === false
            || $identifiantParking === null) {
            $_SESSION['parking_poc_flash'] = ['error', 'Données invalides pour l’entrée.'];
        } else {
            $presentateur = new EntrerParkingPresentateur();
            $useCase = new EntrerParkingUseCase(
                $depotClients,
                $depotParkingParId,
                $depotReservations,
                $depotStationnements,
                new HorlogeSysteme()
            );

            try {
                $useCase->executer(new RequeteEntrerParking($courriel, $identifiantParking), $presentateur);
                $modeleVue = $presentateur->modeleVue;
                $typeMessage = $modeleVue->reussite ? 'success' : 'error';
                $_SESSION['parking_poc_flash'] = [$typeMessage, $modeleVue->message];
            } catch (Throwable $erreurTechnique) {
                error_log((string) $erreurTechnique);
                $_SESSION['parking_poc_flash'] = [
                    'error',
                    'Erreur technique pendant l’entrée. Consultez le journal PHP.',
                ];
            }
        }
    } else {
        http_response_code(400);
        exit('Action inconnue.');
    }

    // L’adaptateur web conserve les objets métier dans la session pour les requêtes suivantes.
    $etatSession = [
        'parkings' => $depotParkings->tous(),
        'customers' => $depotClients->tous(),
        'reservations' => $depotReservations->tous(),
        'sessions' => $depotStationnements->tous(),
    ];
    redirigerApresPost();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Méthode non autorisée.');
}

$latitudeCentre = 47.9029;
$longitudeCentre = 1.9086;
$rayonKm = 20.0;
$erreurRecherche = null;
if (isset($_GET['lat']) || isset($_GET['lon']) || isset($_GET['radius'])) {
    $latitudeBrute = $_GET['lat'] ?? null;
    $longitudeBrute = $_GET['lon'] ?? null;
    $rayonBrut = $_GET['radius'] ?? null;

    if (is_string($latitudeBrute) && is_numeric($latitudeBrute)
        && is_string($longitudeBrute) && is_numeric($longitudeBrute)
        && is_string($rayonBrut) && is_numeric($rayonBrut)) {
        $latitudeCandidate = (float) $latitudeBrute;
        $longitudeCandidate = (float) $longitudeBrute;
        $rayonCandidate = (float) $rayonBrut;

        if (is_finite($latitudeCandidate) && $latitudeCandidate >= -90 && $latitudeCandidate <= 90
            && is_finite($longitudeCandidate) && $longitudeCandidate >= -180 && $longitudeCandidate <= 180
            && is_finite($rayonCandidate) && $rayonCandidate > 0 && $rayonCandidate <= 500) {
            $latitudeCentre = $latitudeCandidate;
            $longitudeCentre = $longitudeCandidate;
            $rayonKm = $rayonCandidate;
        } else {
            $erreurRecherche = 'Coordonnées ou rayon hors limites.';
        }
    } else {
        $erreurRecherche = 'Coordonnées et rayon doivent être des nombres valides.';
    }
}

$presentateurCarte = new LocaliserParkingsPresentateur();
$horloge = new HorlogeSysteme();
$useCaseLocaliser = new LocaliserParkingsUseCase(
    $depotParkings,
    $depotReservations,
    $depotStationnements,
    $horloge
);
$useCaseLocaliser->executer(
    new RequeteLocaliserParkings($latitudeCentre, $longitudeCentre, $rayonKm),
    $presentateurCarte
);
$optionsParkings = $presentateurCarte->modeleVue->parkings;
$heureCalcul = $presentateurCarte->modeleVue->heureCalcul;

$presentateurPage = new RecapitulatifDemoPresentateur();
$useCaseRecapitulatif = new LireRecapitulatifDemoUseCase(
    $depotParkings,
    $depotClients,
    $depotReservations,
    $depotStationnements
);
$useCaseRecapitulatif->executer(new RequeteRecapitulatifDemo(), $presentateurPage);
$modelePage = $presentateurPage->modeleVue;
$tousLesParkings = $modelePage->optionsParkings;
$optionsClients = $modelePage->optionsClients;
$recapitulatifReservations = $modelePage->recapitulatifReservations;
$recapitulatifStationnements = $modelePage->recapitulatifStationnements;

$flash = $_SESSION['parking_poc_flash'] ?? null;
unset($_SESSION['parking_poc_flash']);
$jeton = $_SESSION['parking_poc_csrf'];
$heurePage = $horloge->maintenant();
$debutParDefaut = $heurePage->setTime((int) $heurePage->format('H'), (int) $heurePage->format('i'));
$finParDefaut = $debutParDefaut->modify('+1 hour');
$donneesParkingsJson = json_encode(
    $optionsParkings,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
);

require __DIR__ . '/views/home.php';
