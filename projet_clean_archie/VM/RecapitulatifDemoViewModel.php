<?php

final class RecapitulatifDemoViewModel
{
    public function __construct(
        public readonly array $optionsParkings,
        public readonly array $optionsClients,
        public readonly array $recapitulatifReservations,
        public readonly array $recapitulatifStationnements
    ) {}
}
