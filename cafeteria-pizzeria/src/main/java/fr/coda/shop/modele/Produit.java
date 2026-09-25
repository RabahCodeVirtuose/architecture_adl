package fr.coda.shop.modele;

import java.util.Objects;

public abstract class Produit {
    protected final String nom;
    protected final double prixBase;
    protected final Taille taille;

    protected Produit(String nom, double prixBase, Taille taille) {
        if (nom == null || nom.isBlank()) {
            throw new IllegalArgumentException("Le nom du produit ne doit pas être vide.");
        }
        if (prixBase < 0) {
            throw new IllegalArgumentException("Le prix de base ne peut pas être négatif.");
        }
        this.nom = nom;
        this.prixBase = prixBase;
        this.taille = Objects.requireNonNull(taille, "La taille ne doit pas être nulle.");
    }

    public String obtenirNom() {
        return nom;
    }

    public Taille obtenirTaille() {
        return taille;
    }

    public double obtenirPrix() {
        return prixBase;
    }

    public String obtenirDescription() {
        return nom + " (" + taille + ")";
    }

    protected static Taille verifierTaille(Taille taille) {
        return Objects.requireNonNull(taille, "La taille ne doit pas être nulle.");
    }
}
