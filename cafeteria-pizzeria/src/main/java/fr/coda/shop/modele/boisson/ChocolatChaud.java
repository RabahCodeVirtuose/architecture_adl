package fr.coda.shop.modele.boisson;

import fr.coda.shop.modele.Taille;

public final class ChocolatChaud extends Boisson {
    public ChocolatChaud(Taille taille) {
        super("Chocolat chaud", prixPour(taille), taille);
    }

    private static double prixPour(Taille taille) {
        return switch (verifierTaille(taille)) {
            case PETITE -> 3.00;
            case MOYENNE -> 4.00;
            case GRANDE -> 5.00;
        };
    }
}
