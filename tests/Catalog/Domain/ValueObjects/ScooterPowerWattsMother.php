<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterPowerWatts;

class ScooterPowerWattsMother
{

    public static function create(int $value): ScooterPowerWatts
    {
        return new ScooterPowerWatts($value);
    }


    public static function random(int $min = 1000, int $max = 1_000_000): ScooterPowerWatts
    {
        $value = random_int($min, $max);
        return new ScooterPowerWatts($value);
    }
}
