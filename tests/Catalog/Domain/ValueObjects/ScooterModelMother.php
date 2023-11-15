<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterModel;

class ScooterModelMother
{
    public static function create(string $value): ScooterModel
    {
        return new ScooterModel($value);
    }

    public static function random(): ScooterModel
    {
        $faker = Factory::create();
        $value = $faker->words(2, true);

        return new ScooterModel($value);
    }
}
