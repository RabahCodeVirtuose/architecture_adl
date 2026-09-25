package fr.coda.shop.modele.pizza;

import fr.coda.shop.modele.Taille;

public final class PizzaVegetarienne extends Pizza {
    // Prix d'exemple : l'énoncé ne définit pas les prix des pizzas.
    private static final double PRIX_PETITE = 9.00;
    private static final double PRIX_MOYENNE = 11.00;
    private static final double PRIX_GRANDE = 13.00;

    public PizzaVegetarienne(Taille taille) {
        super("Pizza végétarienne", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> PRIX_PETITE;
            case MOYENNE -> PRIX_MOYENNE;
            case GRANDE -> PRIX_GRANDE;
        };
    }
}
