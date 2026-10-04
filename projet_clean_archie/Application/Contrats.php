<?php

interface IRepositoryListeParkings
{
    /** @return Parking[] */
    public function listerParkings(): array;
}

interface IRepositoryParking
{
    public function trouverParkingParId(string $identifiant): ?Parking;
}

interface IRepositoryClientParCourriel
{
    public function trouverClientParCourriel(string $courriel): ?Client;

    /** @return Client[] */
    public function tous(): array;
}

interface IRepositoryReservations
{
    /** @return Reservation[] */
    public function trouverReservationsEnConflit(string $identifiantParking, DateTimeImmutable $debut, DateTimeImmutable $fin): array;
    /** @return Reservation[] */
    public function trouverReservationsClientParking(string $identifiantClient, string $identifiantParking): array;
    /** @return Reservation[] */
    public function trouverReservationsActives(string $identifiantParking, DateTimeImmutable $instant): array;
    /** @return Reservation[] */
    public function tous(): array;

    public function enregistrer(Reservation $reservation): void;
}

interface IRepositoryStationnements
{
    /** @return Stationnement[] */
    public function trouverStationnementsOuvertsParParking(string $identifiantParking): array;
    public function trouverStationnementOuvertParClient(string $identifiantClient): ?Stationnement;
    public function trouverStationnementOuvertParReservation(string $identifiantReservation): ?Stationnement;
    /** @return Stationnement[] */
    public function tous(): array;

    public function enregistrer(Stationnement $stationnement): void;
}

interface Horloge
{
    public function maintenant(): DateTimeImmutable;
}

interface PortSortieLocalisationParkings
{
    public function transmettre(ReponseLocaliserParkings $reponse): void;
}

interface PortSortieReservation
{
    public function transmettre(ReponseReserverPlace $reponse): void;
}

interface PortSortieEntreeParking
{
    public function transmettre(ReponseEntrerParking $reponse): void;
}

interface PortSortieRecapitulatifDemo
{
    public function transmettre(ReponseRecapitulatifDemo $reponse): void;
}
