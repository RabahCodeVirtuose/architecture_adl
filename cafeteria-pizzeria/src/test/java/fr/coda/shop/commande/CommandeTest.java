package fr.coda.shop.commande;

import fr.coda.shop.fabrique.FabriqueCafeteria;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import java.util.List;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertThrows;

class CommandeTest {
    @Test
    void calculeLePrixFinalDesProduits() {
        FabriqueCafeteria cafeteria = new FabriqueCafeteria();
        Produit cafe = cafeteria.creerSupplement("caramel", cafeteria.creerProduit("café", Taille.MOYENNE));
        Commande commande = new Commande();
        commande.ajouterProduit(cafe);
        commande.ajouterProduit(cafeteria.creerProduit("thé", Taille.PETITE));

        assertEquals(4.00, commande.obtenirPrixTotal(), 0.0001);
    }

    @Test
    void protegeLaListeInterne() {
        Commande commande = new Commande();
        commande.ajouterProduit(new FabriqueCafeteria().creerProduit("café", Taille.PETITE));

        List<Produit> produits = commande.obtenirProduits();
        assertThrows(UnsupportedOperationException.class, produits::clear);
        assertEquals(1, commande.obtenirProduits().size());
    }

    @Test
    void refuseUnProduitNul() {
        assertThrows(NullPointerException.class, () -> new Commande().ajouterProduit(null));
    }
}
