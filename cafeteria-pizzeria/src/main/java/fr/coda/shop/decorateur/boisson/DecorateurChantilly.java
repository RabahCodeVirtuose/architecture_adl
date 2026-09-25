package fr.coda.shop.decorateur.boisson;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.modele.Produit;

public final class DecorateurChantilly extends DecorateurProduit {
    public DecorateurChantilly(Produit produit) {
        super(produit);
    }

    @Override
    public double obtenirPrix() {
        return produit.obtenirPrix() + switch (obtenirTaille()) {
            case PETITE -> 0.50;
            case MOYENNE -> 1.00;
            case GRANDE -> 1.50;
        };
    }

    @Override
    public String obtenirDescription() {
        return produit.obtenirDescription() + " + chantilly";
    }
}
