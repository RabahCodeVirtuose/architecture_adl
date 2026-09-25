package fr.coda.shop.modele.pizza;

import fr.coda.shop.modele.Taille;

public final class QuatreFromages extends Pizza {
    // Prix d'exemple : l'énoncé ne définit pas les prix des pizzas.
    private static final double PRIX_PETITE = 10.00;
    private static final double PRIX_MOYENNE = 12.00;
    private static final double PRIX_GRANDE = 14.00;

    public QuatreFromages(Taille taille) {
        super("Quatre fromages", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> PRIX_PETITE;
            case MOYENNE -> PRIX_MOYENNE;
            case GRANDE -> PRIX_GRANDE;
        };
    }
}
