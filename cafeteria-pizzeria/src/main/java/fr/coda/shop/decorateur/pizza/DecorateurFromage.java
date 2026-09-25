package fr.coda.shop.decorateur.pizza;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.modele.Produit;

public final class DecorateurFromage extends DecorateurProduit {
    // Prix d'exemple : l'énoncé ne définit pas les prix des suppléments de pizza.
    private static final double PRIX = 1.50;

    public DecorateurFromage(Produit produit) {
        super(produit);
    }

    @Override
    public double obtenirPrix() {
        return produit.obtenirPrix() + PRIX;
    }

    @Override
    public String obtenirDescription() {
        return produit.obtenirDescription() + " + fromage";
    }
}
