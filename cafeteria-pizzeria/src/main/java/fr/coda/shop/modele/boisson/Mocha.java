package fr.coda.shop.modele.boisson;

import fr.coda.shop.modele.Taille;

public final class Mocha extends Boisson {
    public Mocha(Taille taille) {
        super("Mocha", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> 5.00;
            case MOYENNE -> 6.50;
            case GRANDE -> 7.50;
        };
    }
}
