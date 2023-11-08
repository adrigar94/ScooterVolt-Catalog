<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterTravelRangeKm;

class ScooterTravelRangeKmMother
{

    public static function create(int $value): ScooterTravelRangeKm
    {
        return new ScooterTravelRangeKm($value);
    }


    public static function random(int $min = 10, int $max = 100): ScooterTravelRangeKm
    {
        $value = random_int($min, $max);
        return new ScooterTravelRangeKm($value);
    }
}
