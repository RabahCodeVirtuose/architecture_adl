package fr.coda.shop.decorateur;

import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import java.util.Objects;

public abstract class DecorateurProduit extends Produit {
    protected final Produit produit;

    protected DecorateurProduit(Produit produit) {
        super(Objects.requireNonNull(produit, "Le produit décoré ne doit pas être nul.").obtenirNom(),
                0, produit.obtenirTaille());
        this.produit = produit;
    }

    @Override
    public String obtenirNom() {
        return produit.obtenirNom();
    }

    @Override
    public Taille obtenirTaille() {
        return produit.obtenirTaille();
    }
}
