package fr.coda.shop.modele.boisson;

import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;

public abstract class Boisson extends Produit {
    protected Boisson(String nom, double prixBase, Taille taille) {
        super(nom, prixBase, taille);
    }
}
