<?php

final class EntreeParkingViewModel
{
    public function __construct(
        public readonly bool $reussite,
        public readonly string $message
    ) {}
}
