package fr.coda.shop.modele.boisson;

import fr.coda.shop.modele.Taille;

public final class Cafe extends Boisson {
    public Cafe(Taille taille) {
        super("Café", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> 1.00;
            case MOYENNE -> 1.50;
            case GRANDE -> 2.00;
        };
    }
}
