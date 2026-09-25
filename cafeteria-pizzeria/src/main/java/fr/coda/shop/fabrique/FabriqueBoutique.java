package fr.coda.shop.fabrique;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;

public interface FabriqueBoutique {
    Produit creerProduit(String type, Taille taille);

    DecorateurProduit creerSupplement(String type, Produit produit);
}
