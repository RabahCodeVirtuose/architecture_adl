package fr.coda.shop.decorateur.boisson;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.modele.Produit;

public final class DecorateurCaramel extends DecorateurProduit {
    private static final double PRIX = 0.50;

    public DecorateurCaramel(Produit produit) {
        super(produit);
    }

    @Override
    public double obtenirPrix() {
        return produit.obtenirPrix() + PRIX;
    }

    @Override
    public String obtenirDescription() {
        return produit.obtenirDescription() + " + caramel";
    }
}
