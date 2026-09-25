package fr.coda.shop.fabrique;

import fr.coda.shop.modele.Taille;
import fr.coda.shop.modele.pizza.Margherita;
import fr.coda.shop.modele.pizza.PizzaVegetarienne;
import fr.coda.shop.modele.pizza.QuatreFromages;
import org.junit.jupiter.api.Test;

import static org.junit.jupiter.api.Assertions.assertInstanceOf;
import static org.junit.jupiter.api.Assertions.assertThrows;

class FabriquePizzeriaTest {
    private final FabriquePizzeria fabrique = new FabriquePizzeria();

    @Test
    void creeToutesLesPizzas() {
        assertInstanceOf(Margherita.class, fabrique.creerProduit("margherita", Taille.PETITE));
        assertInstanceOf(QuatreFromages.class, fabrique.creerProduit("quatre fromages", Taille.MOYENNE));
        assertInstanceOf(PizzaVegetarienne.class, fabrique.creerProduit("végétarienne", Taille.GRANDE));
    }

    @Test
    void refuseUnSupplementDeBoisson() {
        assertThrows(IllegalArgumentException.class,
                () -> fabrique.creerSupplement("caramel", fabrique.creerProduit("margherita", Taille.PETITE)));
    }
}
