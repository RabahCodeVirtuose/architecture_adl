<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Parking partagé — démonstration CODA</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
  <h1>Parking partagé</h1>
  <p>POC CODA · données fictives · fuseau Europe/Paris</p>
</header>

<main>
  <?php if (is_array($flash)): ?>
    <p class="notice <?= echapperHtml($flash[0]) ?>" role="status"><?= echapperHtml($flash[1]) ?></p>
  <?php endif; ?>

  <?php if ($erreurRecherche !== null): ?>
    <p class="notice error">
      <?= echapperHtml($erreurRecherche) ?> La recherche utilise le centre par défaut d’Orléans.
    </p>
  <?php endif; ?>

  <section>
    <h2>Rechercher sur la carte</h2>
    <form method="get">
      <label>
        Latitude
        <input name="lat" type="number" step="any" min="-90" max="90" value="<?= echapperHtml((string) $latitudeCentre) ?>" required>
      </label>
      <label>
        Longitude
        <input name="lon" type="number" step="any" min="-180" max="180" value="<?= echapperHtml((string) $longitudeCentre) ?>" required>
      </label>
      <label>
        Rayon (km)
        <input name="radius" type="number" step="any" min="0.1" max="500" value="<?= echapperHtml((string) $rayonKm) ?>" required>
      </label>
      <button type="submit">Chercher / actualiser l’état</button>
    </form>

    <p class="muted">État calculé à <?= echapperHtml($heureCalcul) ?> (heure Europe/Paris). Une actualisation relance la recherche et recalcule les compteurs.</p>
    <p class="muted">Compteurs de cette session de démonstration uniquement : ils ne représentent pas un parking réel ni un état partagé entre visiteurs.</p>
    <p class="muted">L’état affiché décrit la situation à l’instant indiqué ; il ne préjuge pas de la disponibilité du créneau choisi. Le use case de réservation vérifiera ce créneau.</p>

    <ul class="legend" aria-label="Légende de disponibilité">
      <li><span class="legend-dot available"></span> Disponible</li>
      <li><span class="legend-dot partial"></span> Partiellement occupé ou réservé</li>
      <li><span class="legend-dot full"></span> Complet</li>
      <li><span class="legend-dot closed"></span> Fermé</li>
    </ul>

    <p id="map-message" aria-live="polite"></p>
    <div id="parking-map" data-center-lat="<?= echapperHtml((string) $latitudeCentre) ?>" data-center-lon="<?= echapperHtml((string) $longitudeCentre) ?>" role="application" aria-label="Carte des parkings trouvés"></div>

    <div id="parking-results-fallback">
      <h3>Liste des parkings</h3>
      <ul id="parking-list" class="parking-list">
      <?php foreach ($optionsParkings as $parking): ?>
        <li class="parking-card status-<?= echapperHtml($parking['statusClass']) ?>">
          <h4><?= echapperHtml($parking['name']) ?></h4>
          <p><?= echapperHtml($parking['addressLabel']) ?></p>
          <p><strong>État :</strong> <?= echapperHtml($parking['statusLabel']) ?></p>
          <p>
            <strong>Capacité :</strong> <?= echapperHtml((string) $parking['totalSpots']) ?> ·
            <strong>Véhicules présents :</strong> <?= echapperHtml((string) $parking['vehiclesPresent']) ?>
          </p>
          <p>
            <strong>Places indisponibles maintenant :</strong> <?= echapperHtml((string) $parking['unavailableSpots']) ?> ·
            <strong>Disponibles maintenant :</strong> <?= echapperHtml((string) $parking['availableSpots']) ?>
          </p>
          <p><strong>Ouverture :</strong> <?= $parking['isOpen'] ? 'Ouvert' : 'Fermé' ?></p>
          <?php if (!$parking['isOpen']): ?>
            <p class="muted">Aucune entrée ou réservation immédiate pendant la fermeture ; un autre créneau peut rester réservable.</p>
          <?php endif; ?>
          <p><strong>Horaires :</strong> <?= echapperHtml($parking['openingHoursLabel']) ?></p>
          <p><strong>Tarifs :</strong> <?= echapperHtml($parking['tariffLabel']) ?></p>
          <button class="choose-parking" type="button" data-parking-id="<?= echapperHtml($parking['id']) ?>">Choisir ce parking</button>
        </li>
      <?php endforeach; ?>

      <?php if ($optionsParkings === []): ?>
        <li>Aucun parking dans les résultats actuels.</li>
      <?php endif; ?>
      </ul>
    </div>
  </section>

  <section>
    <h2>Réserver une place</h2>
    <p class="muted">Le choix d’un client fictif sert uniquement à la démonstration ; ce n’est pas une authentification.</p>
    <form id="reservation-form" method="post">
      <input type="hidden" name="csrf" value="<?= echapperHtml($jeton) ?>">
      <input type="hidden" name="action" value="reserve">
      <label>
        Client fictif
        <select name="client_email" required>
          <?php foreach ($optionsClients as $client): ?>
            <option value="<?= echapperHtml($client['email']) ?>"><?= echapperHtml($client['name']) ?> — <?= echapperHtml($client['email']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        Parking trouvé
        <select id="parking-id" name="parking_id" required>
          <?php foreach ($optionsParkings as $parking): ?>
            <option value="<?= echapperHtml($parking['id']) ?>"><?= echapperHtml($parking['name']) ?> — <?= echapperHtml($parking['statusLabel']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        Début
        <input type="datetime-local" name="start" value="<?= echapperHtml(formaterDatePourChamp($debutParDefaut)) ?>" required>
      </label>
      <label>
        Fin
        <input type="datetime-local" name="end" value="<?= echapperHtml(formaterDatePourChamp($finParDefaut)) ?>" required>
      </label>
      <button type="submit" <?= $optionsParkings === [] ? 'disabled' : '' ?>>Confirmer la réservation</button>
    </form>
  </section>

  <section>
    <h2>Enregistrer une entrée</h2>
    <p>L’heure d’entrée est fournie par le serveur. Faites d’abord une réservation couvrant l’heure actuelle, puis enregistrez l’entrée.</p>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= echapperHtml($jeton) ?>">
      <input type="hidden" name="action" value="enter">
      <label>
        Client
        <select name="client_email">
          <?php foreach ($optionsClients as $client): ?>
            <option value="<?= echapperHtml($client['email']) ?>"><?= echapperHtml($client['name']) ?> — <?= echapperHtml($client['email']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        Parking
        <select name="parking_id">
          <?php foreach ($tousLesParkings as $parking): ?>
            <option value="<?= echapperHtml($parking['id']) ?>"><?= echapperHtml($parking['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit">Simuler l’entrée et l’ouverture de la porte</button>
    </form>
  </section>

  <section>
    <h2>État de cette session de démonstration</h2>
    <div class="grid">
      <div>
        <h3>Réservations</h3>
        <ul>
          <?php foreach ($recapitulatifReservations as $ligne): ?>
            <li><?= echapperHtml($ligne['email']) ?> — <?= echapperHtml($ligne['parking']) ?> : <?= echapperHtml($ligne['start']) ?> à <?= echapperHtml($ligne['end']) ?></li>
          <?php endforeach; ?>
          <?php if ($recapitulatifReservations === []): ?>
            <li>Aucune réservation dans cette session.</li>
          <?php endif; ?>
        </ul>
      </div>
      <div>
        <h3>Stationnements</h3>
        <ul>
          <?php foreach ($recapitulatifStationnements as $ligne): ?>
            <li><?= echapperHtml($ligne['email']) ?> — <?= echapperHtml($ligne['parking']) ?>, entrée à <?= echapperHtml($ligne['entered']) ?><?= $ligne['open'] ? ' (en cours)' : '' ?></li>
          <?php endforeach; ?>
          <?php if ($recapitulatifStationnements === []): ?>
            <li>Aucune entrée enregistrée.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= echapperHtml($jeton) ?>">
      <input type="hidden" name="action" value="reset">
      <button class="secondary" type="submit">Réinitialiser les données de cette session</button>
    </form>
  </section>

  <p class="muted">Le fond OpenStreetMap nécessite Internet. Attribution © OpenStreetMap contributors, données sous licence ODbL. Les tuiles ne sont pas préchargées ni conservées hors ligne.</p>
</main>

<script id="parking-data" type="application/json"><?= $donneesParkingsJson ?></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="assets/app.js" defer></script>
</body>
</html>
