<?php

final class HorlogeSysteme implements Horloge
{
    public function maintenant(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));
    }
}
