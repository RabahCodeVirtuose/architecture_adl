package fr.coda.shop.decorateur;

import fr.coda.shop.fabrique.FabriqueCafeteria;
import fr.coda.shop.fabrique.FabriquePizzeria;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

class DecorateurProduitTest {
    private final FabriqueCafeteria cafeteria = new FabriqueCafeteria();

    @Test
    void cumulePlusieursSupplementsEtDescriptions() {
        Produit produit = cafeteria.creerProduit("mocha", Taille.MOYENNE);
        produit = cafeteria.creerSupplement("caramel", produit);
        assertTrue(produit.obtenirDescription().endsWith("+ caramel"));

        produit = cafeteria.creerSupplement("coulis chocolat", produit);
        produit = cafeteria.creerSupplement("chantilly", produit);

        assertEquals(9.00, produit.obtenirPrix(), 0.0001);
        assertEquals("Mocha (MOYENNE) + caramel + coulis chocolat + chantilly", produit.obtenirDescription());
    }

    @Test
    void appliqueLePrixDeChantillySelonLaTaille() {
        assertEquals(1.50, prixCafeAvecChantilly(Taille.PETITE), 0.0001);
        assertEquals(2.50, prixCafeAvecChantilly(Taille.MOYENNE), 0.0001);
        assertEquals(3.50, prixCafeAvecChantilly(Taille.GRANDE), 0.0001);
    }

    @Test
    void cumuleLesSupplementsDePizza() {
        FabriquePizzeria pizzeria = new FabriquePizzeria();
        Produit pizza = pizzeria.creerProduit("margherita", Taille.GRANDE);
        pizza = pizzeria.creerSupplement("olive", pizza);

        assertEquals(12.50, pizza.obtenirPrix(), 0.0001);
    }

    private double prixCafeAvecChantilly(Taille taille) {
        Produit cafe = cafeteria.creerProduit("café", taille);
        return cafeteria.creerSupplement("chantilly", cafe).obtenirPrix();
    }
}
