package fr.coda.shop.modele.pizza;

import fr.coda.shop.modele.Taille;

public final class Margherita extends Pizza {
    // Prix d'exemple : l'énoncé ne définit pas les prix des pizzas.
    private static final double PRIX_PETITE = 8.00;
    private static final double PRIX_MOYENNE = 10.00;
    private static final double PRIX_GRANDE = 12.00;

    public Margherita(Taille taille) {
        super("Margherita", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> PRIX_PETITE;
            case MOYENNE -> PRIX_MOYENNE;
            case GRANDE -> PRIX_GRANDE;
        };
    }
}
