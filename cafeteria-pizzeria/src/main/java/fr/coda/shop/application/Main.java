package fr.coda.shop.application;

import fr.coda.shop.commande.Commande;
import fr.coda.shop.fabrique.FabriqueBoutique;
import fr.coda.shop.fabrique.FabriqueCafeteria;
import fr.coda.shop.fabrique.FabriquePizzeria;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;

public final class Main {
    private Main() {
    }

    public static void main(String[] args) {
        FabriqueBoutique cafeteria = new FabriqueCafeteria();
        Produit mocha = cafeteria.creerProduit("mocha", Taille.MOYENNE);
        mocha = cafeteria.creerSupplement("caramel", mocha);
        mocha = cafeteria.creerSupplement("coulis chocolat", mocha);
        mocha = cafeteria.creerSupplement("chantilly", mocha);

        Produit cafe = cafeteria.creerProduit("cafe", Taille.MOYENNE);
        cafe = cafeteria.creerSupplement("caramel", cafe);
        FabriqueBoutique pizzeria = new FabriquePizzeria();
        Produit pizza = pizzeria.creerProduit("margherita", Taille.GRANDE);
        pizza = pizzeria.creerSupplement("fromage", pizza);
        pizza = pizzeria.creerSupplement("champignon", pizza);
        pizza = pizzeria.creerSupplement("olive", pizza);

        Commande commande = new Commande();
        commande.ajouterProduit(mocha);
        commande.ajouterProduit(cafe);
        commande.ajouterProduit(pizza);

        afficher(mocha);
        afficher(cafe);
        afficher(pizza);

        System.out.printf("Total de la commande : %.2f €%n", commande.obtenirPrixTotal());
    }

    private static void afficher(Produit produit) {
        System.out.printf("%s : %.2f €%n", produit.obtenirDescription(), produit.obtenirPrix());
    }
}
