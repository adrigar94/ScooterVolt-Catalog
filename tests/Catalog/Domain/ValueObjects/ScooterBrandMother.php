<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterBrand;

class ScooterBrandMother
{
    public static function create(string $value): ScooterBrand
    {
        return new ScooterBrand($value);
    }

    public static function random(): ScooterBrand
    {
        $faker = Factory::create();
        $value = $faker->company();

        return new ScooterBrand($value);
    }
}
