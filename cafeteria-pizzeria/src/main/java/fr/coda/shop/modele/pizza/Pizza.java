package fr.coda.shop.modele.pizza;

import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;

public abstract class Pizza extends Produit {
    protected Pizza(String nom, double prixBase, Taille taille) {
        super(nom, prixBase, taille);
    }
}
