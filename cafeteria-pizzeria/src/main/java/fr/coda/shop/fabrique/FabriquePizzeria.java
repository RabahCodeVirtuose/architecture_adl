package fr.coda.shop.fabrique;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.decorateur.pizza.DecorateurChampignon;
import fr.coda.shop.decorateur.pizza.DecorateurFromage;
import fr.coda.shop.decorateur.pizza.DecorateurJambon;
import fr.coda.shop.decorateur.pizza.DecorateurOlive;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import fr.coda.shop.modele.pizza.Margherita;
import fr.coda.shop.modele.pizza.PizzaVegetarienne;
import fr.coda.shop.modele.pizza.QuatreFromages;
import java.text.Normalizer;
import java.util.Locale;

public final class FabriquePizzeria implements FabriqueBoutique {
    @Override
    public Produit creerProduit(String type, Taille taille) {
        return switch (normaliser(type)) {
            case "margherita" -> new Margherita(taille);
            case "quatre fromages" -> new QuatreFromages(taille);
            case "vegetarienne", "pizza vegetarienne" -> new PizzaVegetarienne(taille);
            default -> throw nonPrisEnCharge("produit", type);
        };
    }

    @Override
    public DecorateurProduit creerSupplement(String type, Produit produit) {
        return switch (normaliser(type)) {
            case "fromage" -> new DecorateurFromage(produit);
            case "champignon" -> new DecorateurChampignon(produit);
            case "jambon" -> new DecorateurJambon(produit);
            case "olive" -> new DecorateurOlive(produit);
            default -> throw nonPrisEnCharge("supplément", type);
        };
    }

    private static String normaliser(String type) {
        if (type == null || type.isBlank()) {
            throw new IllegalArgumentException("Le type ne doit pas être vide.");
        }
        return Normalizer.normalize(type.trim(), Normalizer.Form.NFD)
                .replaceAll("\\p{M}", "")
                .toLowerCase(Locale.ROOT);
    }

    private static IllegalArgumentException nonPrisEnCharge(String categorie, String type) {
        return new IllegalArgumentException("La pizzeria ne propose pas ce " + categorie + " : " + type);
    }
}
