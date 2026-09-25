package fr.coda.shop.commande;

import fr.coda.shop.modele.Produit;
import java.util.ArrayList;
import java.util.List;
import java.util.Objects;

public final class Commande {
    private final List<Produit> produits = new ArrayList<>();

    public void ajouterProduit(Produit produit) {
        produits.add(Objects.requireNonNull(produit, "Le produit ne doit pas être nul."));
    }

    public void retirerProduit(Produit produit) {
        produits.remove(produit);
    }

    public double obtenirPrixTotal() {
        return produits.stream().mapToDouble(Produit::obtenirPrix).sum();
    }

    public List<Produit> obtenirProduits() {
        return List.copyOf(produits);
    }
}
