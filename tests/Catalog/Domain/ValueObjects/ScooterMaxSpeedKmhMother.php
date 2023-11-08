<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterMaxSpeedKmh;

class ScooterMaxSpeedKmhMother
{

    public static function create(int $value): ScooterMaxSpeedKmh
    {
        return new ScooterMaxSpeedKmh($value);
    }


    public static function random(int $min = 10, int $max = 100): ScooterMaxSpeedKmh
    {
        $value = random_int($min, $max);
        return new ScooterMaxSpeedKmh($value);
    }
}
