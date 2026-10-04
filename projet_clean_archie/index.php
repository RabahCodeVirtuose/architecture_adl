<?php
// Point d'entrée conservé pour les serveurs qui ouvrent directement la racine du projet.
// Pour le lancement recommandé, utiliser public/ comme racine web.
header('Location: public/', true, 302);
exit;
