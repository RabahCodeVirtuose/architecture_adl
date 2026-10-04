(() => {
  const elementCarte = document.getElementById('parking-map');
  const messageCarte = document.getElementById('map-message');
  const selecteurParking = document.getElementById('parking-id');
  const listeSecours = document.getElementById('parking-results-fallback');
  let parkingsTrouves = [];
  try {
    parkingsTrouves = JSON.parse(document.getElementById('parking-data').textContent);
  } catch (erreurJson) {
    messageCarte.textContent = 'Les données de la carte ne sont pas disponibles. La liste reste consultable.';
  }

  if (!window.L || !elementCarte) {
    messageCarte.textContent = 'La carte est indisponible (connexion Internet requise). La liste HTML reste consultable.';
    return;
  }

  const centreRecherche = [Number(elementCarte.dataset.centerLat), Number(elementCarte.dataset.centerLon)];
  const carte = L.map(elementCarte).setView(centreRecherche, 13);
  let erreursTuilesPendantChargement = false;
  let tuilesChargeesAvecSucces = 0;
  const tuilesCarte = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  });
  tuilesCarte.on('loading', () => {
    erreursTuilesPendantChargement = false;
    tuilesChargeesAvecSucces = 0;
  });
  tuilesCarte.on('tileerror', () => {
    erreursTuilesPendantChargement = true;
    listeSecours.hidden = false;
    messageCarte.textContent = 'Fond cartographique indisponible (connexion Internet requise). La liste reste disponible.';
  });
  tuilesCarte.on('tileload', () => {
    tuilesChargeesAvecSucces++;
  });
  tuilesCarte.on('load', () => {
    if (tuilesChargeesAvecSucces > 0 && !erreursTuilesPendantChargement && parkingsTrouves.length > 0) {
      messageCarte.textContent = '';
      listeSecours.hidden = true;
    }
  });
  tuilesCarte.addTo(carte);

  const marqueurs = [];
  document.querySelectorAll('.choose-parking').forEach((bouton) => {
    bouton.addEventListener('click', () => {
      if (selecteurParking) {
        selecteurParking.value = bouton.dataset.parkingId;
      }
      document.getElementById('reservation-form').scrollIntoView({ behavior: 'smooth' });
    });
  });
  const classesMarqueurs = {
    available: 'available',
    partial: 'partial',
    full: 'full',
    closed: 'closed'
  };
  for (const parking of parkingsTrouves) {
    const elementMarqueur = document.createElement('span');
    const statutMarqueur = classesMarqueurs[parking.status] || 'closed';
    elementMarqueur.classList.add('parking-marker', `parking-marker-${statutMarqueur}`);
    elementMarqueur.textContent = 'P';
    elementMarqueur.setAttribute('aria-label', parking.statusLabel);
    const icone = L.divIcon({
      className: 'parking-marker-container',
      html: elementMarqueur,
      iconSize: [32, 38],
      iconAnchor: [16, 36]
    });
    const marqueur = L.marker([parking.latitude, parking.longitude], { icon: icone }).addTo(carte);

    const contenuPopup = document.createElement('div');
    contenuPopup.classList.add('parking-popup-content');
    const titrePopup = document.createElement('strong');
    titrePopup.textContent = parking.name;
    contenuPopup.append(titrePopup);

    const ajouterDetail = (libelle, valeur) => {
      const lignePopup = document.createElement('p');
      const elementLibelle = document.createElement('strong');
      elementLibelle.textContent = `${libelle} : `;
      lignePopup.append(elementLibelle, document.createTextNode(String(valeur)));
      contenuPopup.append(lignePopup);
    };
    ajouterDetail('Adresse', parking.addressLabel);
    ajouterDetail('État', parking.statusLabel);
    ajouterDetail('Capacité totale', `${parking.totalSpots} place(s)`);
    ajouterDetail('Véhicules présents', `${parking.vehiclesPresent}`);
    ajouterDetail('Places indisponibles maintenant', `${parking.unavailableSpots}`);
    ajouterDetail('Places disponibles maintenant', `${parking.availableSpots}`);
    ajouterDetail('Ouverture maintenant', parking.isOpen ? 'Ouvert' : 'Fermé');
    ajouterDetail('Horaires', parking.openingHoursLabel);
    ajouterDetail('Tarifs', parking.tariffLabel);
    if (!parking.isOpen) {
      ajouterDetail('Réservation', 'Aucune entrée ou réservation immédiate pendant la fermeture. Un autre créneau peut rester disponible.');
    }
    const boutonChoisir = document.createElement('button');
    boutonChoisir.type = 'button';
    boutonChoisir.textContent = 'Choisir ce parking';
    boutonChoisir.addEventListener('click', () => {
      if (selecteurParking) {
        selecteurParking.value = parking.id;
      }
      document.getElementById('reservation-form').scrollIntoView({ behavior: 'smooth' });
    });
    contenuPopup.append(boutonChoisir);
    marqueur.bindPopup(contenuPopup, {
      maxHeight: 300,
      maxWidth: 340,
      autoPan: true
    });
    marqueur.on('click', () => {
      if (selecteurParking) {
        selecteurParking.value = parking.id;
      }
    });
    marqueurs.push(marqueur);
  }

  if (marqueurs.length > 1) {
    carte.fitBounds(L.featureGroup(marqueurs).getBounds().pad(0.15));
  } else if (marqueurs.length === 1) {
    carte.setView(marqueurs[0].getLatLng(), 15);
  } else {
    messageCarte.textContent = 'Aucun parking dans ce rayon. Modifiez la recherche pour afficher des résultats.';
  }
})();
