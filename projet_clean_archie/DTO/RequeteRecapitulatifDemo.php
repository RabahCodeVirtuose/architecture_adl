<?php

final class RequeteRecapitulatifDemo {}

final class ReponseRecapitulatifDemo
{
    public function __construct(
        public readonly array $optionsParkings,
        public readonly array $optionsClients,
        public readonly array $reservations,
        public readonly array $stationnements
    ) {}
}
