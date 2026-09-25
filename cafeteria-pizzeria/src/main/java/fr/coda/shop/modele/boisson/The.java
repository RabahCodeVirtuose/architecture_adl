package fr.coda.shop.modele.boisson;

import fr.coda.shop.modele.Taille;

public final class The extends Boisson {
    public The(Taille taille) {
        super("Thé", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> 2.00;
            case MOYENNE -> 2.50;
            case GRANDE -> 3.00;
        };
    }
}
