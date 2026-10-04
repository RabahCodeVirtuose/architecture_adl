<?php

/** Horaires hebdomadaires en heure locale ; une plage peut dépasser le jour de début. */
final class OpeningHours
{
    /** @param array<int, array<array{start:string,end:string,endDayOffset:int}>> $weeklyWindows */
    public function __construct(
        private readonly bool $alwaysOpen,
        private readonly array $weeklyWindows = [],
        private readonly string $timezone = 'Europe/Paris'
    ) {}

    public static function toujoursOuvert(): self
    {
        return new self(true);
    }

    public function estOuvertA(DateTimeImmutable $instant): bool
    {
        if ($this->alwaysOpen) {
            return true;
        }

        $fuseau = new DateTimeZone($this->timezone);
        $instantLocal = $instant->setTimezone($fuseau);
        $premierJour = $instantLocal->setTime(0, 0)->modify('-6 days');
        $dernierJour = $instantLocal->setTime(0, 0);

        for ($jour = $premierJour; $jour <= $dernierJour; $jour = $jour->modify('+1 day')) {
            $numeroJour = (int) $jour->format('N');
            foreach ($this->weeklyWindows[$numeroJour] ?? [] as $plage) {
                $debutPlage = DateTimeImmutable::createFromFormat(
                    '!Y-m-d H:i', $jour->format('Y-m-d') . ' ' . $plage['start'], $fuseau
                );
                $jourFin = $jour->modify('+' . $plage['endDayOffset'] . ' days');
                $finPlage = DateTimeImmutable::createFromFormat(
                    '!Y-m-d H:i', $jourFin->format('Y-m-d') . ' ' . $plage['end'], $fuseau
                );

                if ($debutPlage && $finPlage && $debutPlage <= $instant && $instant < $finPlage) {
                    return true;
                }
            }
        }

        return false;
    }

    public function estToujoursOuvert(): bool
    {
        return $this->alwaysOpen;
    }

    /** @return array<int, array<array{start:string,end:string,endDayOffset:int}>> */
    public function plagesHebdomadaires(): array
    {
        return $this->weeklyWindows;
    }

    public function fuseauHoraire(): string
    {
        return $this->timezone;
    }

    public function couvre(DateTimeImmutable $debut, DateTimeImmutable $fin): bool
    {
        if ($debut >= $fin) {
            return false;
        }
        if ($this->alwaysOpen) {
            return true;
        }

        $fuseau = new DateTimeZone($this->timezone);
        $debutLocal = $debut->setTimezone($fuseau);
        $finLocale = $fin->setTimezone($fuseau);
        $premierJour = $debutLocal->setTime(0, 0)->modify('-6 days');
        $dernierJour = $finLocale->setTime(0, 0);
        $plagesQuiCroisent = [];

        for ($jour = $premierJour; $jour <= $dernierJour; $jour = $jour->modify('+1 day')) {
            $numeroJour = (int) $jour->format('N');
            foreach ($this->weeklyWindows[$numeroJour] ?? [] as $plage) {
                $debutPlage = DateTimeImmutable::createFromFormat(
                    '!Y-m-d H:i', $jour->format('Y-m-d') . ' ' . $plage['start'], $fuseau
                );
                $jourFin = $jour->modify('+' . $plage['endDayOffset'] . ' days');
                $finPlage = DateTimeImmutable::createFromFormat(
                    '!Y-m-d H:i', $jourFin->format('Y-m-d') . ' ' . $plage['end'], $fuseau
                );

                if ($debutPlage && $finPlage && $finPlage > $debutPlage
                    && $finPlage > $debut && $debutPlage < $fin) {
                    $plagesQuiCroisent[] = [$debutPlage, $finPlage];
                }
            }
        }

        usort($plagesQuiCroisent, static fn(array $premiere, array $seconde): int => $premiere[0] <=> $seconde[0]);
        $couvertureJusqua = $debut;
        foreach ($plagesQuiCroisent as [$debutPlage, $finPlage]) {
            if ($debutPlage > $couvertureJusqua) {
                return false;
            }
            if ($finPlage > $couvertureJusqua) {
                $couvertureJusqua = $finPlage;
            }
            if ($couvertureJusqua >= $fin) {
                return true;
            }
        }

        return false;
    }
}

// Alias français ; l'ancien nom reste nécessaire pour les objets de session déjà enregistrés.
class_alias(OpeningHours::class, 'HorairesOuverture');
