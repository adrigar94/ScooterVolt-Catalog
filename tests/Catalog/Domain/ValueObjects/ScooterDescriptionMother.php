<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterDescription;

class ScooterDescriptionMother
{
    public static function create(string $value): ScooterDescription
    {
        return new ScooterDescription($value);
    }

    public static function random(): ScooterDescription
    {
        $faker = Factory::create();
        $value = $faker->paragraphs(2, true);

        return new ScooterDescription($value);
    }
}
