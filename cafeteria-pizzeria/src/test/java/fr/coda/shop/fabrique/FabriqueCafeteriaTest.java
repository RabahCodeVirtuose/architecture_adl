package fr.coda.shop.fabrique;

import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import fr.coda.shop.modele.boisson.Cafe;
import fr.coda.shop.modele.boisson.ChocolatChaud;
import fr.coda.shop.modele.boisson.Mocha;
import fr.coda.shop.modele.boisson.The;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertInstanceOf;
import static org.junit.jupiter.api.Assertions.assertThrows;

class FabriqueCafeteriaTest {
    private final FabriqueCafeteria fabrique = new FabriqueCafeteria();

    @Test
    void creeToutesLesBoissons() {
        assertInstanceOf(Cafe.class, fabrique.creerProduit("CAFÉ", Taille.PETITE));
        assertInstanceOf(The.class, fabrique.creerProduit("thé", Taille.MOYENNE));
        assertInstanceOf(Mocha.class, fabrique.creerProduit("mocha", Taille.GRANDE));
        assertInstanceOf(ChocolatChaud.class, fabrique.creerProduit("chocolat chaud", Taille.PETITE));
    }

    @Test
    void retourneLePrixDeBaseDuneBoisson() {
        assertEquals(6.50, fabrique.creerProduit("mocha", Taille.MOYENNE).obtenirPrix(), 0.0001);
    }

    @Test
    void refuseUnSupplementDePizza() {
        Produit cafe = fabrique.creerProduit("café", Taille.PETITE);
        assertThrows(IllegalArgumentException.class, () -> fabrique.creerSupplement("fromage", cafe));
    }

    @Test
    void refuseUnProduitDePizzeria() {
        assertThrows(IllegalArgumentException.class,
                () -> fabrique.creerProduit("margherita", Taille.PETITE));
    }
}
