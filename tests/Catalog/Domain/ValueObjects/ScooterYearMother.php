<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterYear;

class ScooterYearMother
{

    public static function create(int $value): ScooterYear
    {
        return new ScooterYear($value);
    }


    public static function random(int $min = 2000, int $max = null): ScooterYear
    {
        if ($max === null) {
            $max = (int) date('Y');
        }
        $value = rand($min, $max);
        return new ScooterYear($value);
    }
}