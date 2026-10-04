# Parking partagé — POC CODA

## Lancer le projet

Prérequis : PHP 8.1 ou supérieur avec les extensions standard `session` et `json`. Le répertoire défini par `session.save_path` dans PHP doit être accessible en écriture. Aucune base de données ni dépendance à installer.

Depuis ce dossier :

```powershell
php -S localhost:8000 -t public
```

Avec PHP installé dans XAMPP :

```powershell
& "C:\xampp\php\php.exe" -S localhost:8000 -t public
```

Puis ouvrir <http://localhost:8000>. Le dossier `public/` doit être la racine web. Pour Apache/XAMPP, configurez le `DocumentRoot` (ou l’alias du site) vers le dossier `public/`. L’`index.php` à la racine du dépôt est seulement un raccourci de navigation : il ne protège pas les autres dossiers si le projet entier est servi comme racine web. Ne publiez donc pas le dossier du projet entier.

## Démonstration

Les trois actions sont la recherche géographique des parkings, la réservation et l’enregistrement d’une entrée. Choisir un client fictif ne constitue pas une authentification.

Les données initiales sont créées une seule fois par session PHP standard du navigateur : trois parkings et deux clients fictifs, sans réservation ni stationnement prérempli. Les parkings d’Orléans apparaissent dans le rayon initial de 20 km ; `park_loiret_exterieur` est situé au-delà. Pour tester l’entrée, crée une réservation dont le créneau couvre l’heure actuelle, puis utilise le formulaire d’entrée. Le bouton de réinitialisation recrée les parkings et clients et vide les réservations et stationnements de la session courante. Les visiteurs ont des sessions isolées : ce n’est pas un stockage partagé ou durable. Le projet utilise le `session.save_path` configuré par PHP et ne crée pas de dossier de session dans le dépôt.

## Conventions métier du POC

- Les créneaux sont `[début, fin)` : une réservation cesse d’occuper sa place à son heure de fin.
- Les dates saisies et les horaires hebdomadaires utilisent `Europe/Paris`. Les plages hebdomadaires peuvent traverser minuit et plusieurs jours, y compris la limite de semaine. Une réservation doit être entièrement couverte par les plages d’ouverture.
- Les prix sont calculés en centimes par tranches de 15 minutes, arrondies au supérieur. Les paliers tarifaires dépendent de la durée cumulée de la réservation. Le montant est affiché en euros.
- Une réservation compte même si son conducteur n’est pas entré. Une entrée liée à cette réservation ne compte pas une seconde fois. Un stationnement sans sortie reste bloquant après la fin réservée. La sortie n’est pas dans le périmètre de ces trois use cases.
- Les compteurs de carte sont calculés une fois à l’instant serveur affiché : « véhicules présents » compte les entrées toujours ouvertes ; « places indisponibles » compte les réservations actives et les entrées ouvertes non couvertes par une réservation active liée. Les places disponibles sont la capacité moins les places indisponibles, avec un minimum de zéro. L’état courant n’est pas une promesse de disponibilité future.
- Le contrôle de capacité à l’entrée porte sur les véhicules réellement présents et encore stationnés. Une réservation active seule ne représente pas un véhicule arrivé.
- L’entrée utilise l’heure du serveur et simule l’ouverture de porte. Aucun matériel ou paiement n’est intégré.

## Carte et limites

La carte utilise Leaflet 1.9.4 depuis le CDN officiel public, avec intégrité vérifiée. Leaflet est distribué sous licence BSD-2-Clause ([licence de la version 1.9.4](https://github.com/Leaflet/Leaflet/blob/v1.9.4/LICENSE)). Le téléchargement des fichiers officiels dans le dépôt a été bloqué par l’accès réseau de l’environnement ; le navigateur doit donc avoir Internet pour charger Leaflet. Le fond utilise `https://tile.openstreetmap.org/{z}/{x}/{y}.png`, avec attribution visible. Il nécessite Internet et n’est pas préchargé pour un usage hors ligne. En cas d’indisponibilité réseau, la liste HTML reste utilisable.

L’utilisation interactive respecte la [politique des tuiles OSM](https://operations.osmfoundation.org/policies/tiles/) : affichage standard, attribution visible, sans téléchargement massif ni mode hors ligne. La carte n’utilise pas de géocodage ni de clé API.

## Organisation

- `Entity/` : règles de créneau, disponibilité, horaires et tarif.
- `Application/Contrats.php` : interfaces des repositories, de l’horloge et des sorties de présentation.
- `UseCase/` et `DTO/` : requêtes, réponses et actions applicatives.
- `Repository/` : dépôts en mémoire, interchangeables grâce aux interfaces de `Application/`.
- `Presenter/` et `VM/` : présentateurs et modèles de vue en français.
- `Infrastructure/FixturesDemo.php` : données fictives, créées à l’initialisation ou à la réinitialisation de la session.
- `Entity/Client.php`, `Entity/Stationnement.php`, `Entity/HorairesOuverture.php` et `Entity/GrilleTarifaire.php` : noms de fichiers français. Les classes historiques `Customer`, `ParkingSession`, `OpeningHours` et `ParkingTariff` restent déclarées pour relire les objets déjà sérialisés dans les sessions ; des alias français sont utilisés dans le reste du code.
- `public/index.php` : contrôleur/assemblage HTTP, protection CSRF, session et rendu HTML ; `public/assets/` contient la vue côté navigateur.

Les données de démonstration déjà présentes dans l’ancien dossier local `var/sessions/` ont été supprimées lors du passage aux sessions PHP standard. Seuls les fichiers `sess_*` de ce dossier du projet ont été retirés ; aucune session globale PHP n’a été supprimée. PHP doit pouvoir écrire dans son propre `session.save_path`.

Les POST utilisent un jeton CSRF et redirigent après traitement pour éviter une nouvelle réservation ou entrée lors d’une actualisation. Le stockage de session ne protège pas contre plusieurs visiteurs partageant un même compte ni ne remplace une persistance réelle.
