package fr.coda.shop.fabrique;

import fr.coda.shop.decorateur.DecorateurProduit;
import fr.coda.shop.decorateur.boisson.DecorateurCaramel;
import fr.coda.shop.decorateur.boisson.DecorateurChantilly;
import fr.coda.shop.decorateur.boisson.DecorateurCoulisChocolat;
import fr.coda.shop.decorateur.boisson.DecorateurExtraMocha;
import fr.coda.shop.modele.Produit;
import fr.coda.shop.modele.Taille;
import fr.coda.shop.modele.boisson.Cafe;
import fr.coda.shop.modele.boisson.ChocolatChaud;
import fr.coda.shop.modele.boisson.Mocha;
import fr.coda.shop.modele.boisson.The;
import java.text.Normalizer;
import java.util.Locale;

public final class FabriqueCafeteria implements FabriqueBoutique {
    @Override
    public Produit creerProduit(String type, Taille taille) {
        return switch (normaliser(type)) {
            case "cafe" -> new Cafe(taille);
            case "the" -> new The(taille);
            case "mocha" -> new Mocha(taille);
            case "chocolat chaud" -> new ChocolatChaud(taille);
            default -> throw nonPrisEnCharge("produit", type);
        };
    }

    @Override
    public DecorateurProduit creerSupplement(String type, Produit produit) {
        return switch (normaliser(type)) {
            case "caramel" -> new DecorateurCaramel(produit);
            case "coulis chocolat" -> new DecorateurCoulisChocolat(produit);
            case "extra mocha" -> new DecorateurExtraMocha(produit);
            case "chantilly" -> new DecorateurChantilly(produit);
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
        return new IllegalArgumentException("La cafétéria ne propose pas ce " + categorie + " : " + type);
    }
}
