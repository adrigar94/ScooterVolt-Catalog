<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Domain\Location\CoordsValueObject;
use Adrigar94\ValueObjectCraft\Domain\Location\PlaceLocationValueObject;
use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterLocation;

class ScooterLocationMother
{

    public static function create(CoordsValueObject $coords, PlaceLocationValueObject $address): ScooterLocation
    {
        return new ScooterLocation($coords, $address);
    }

    public static function random(): ScooterLocation
    {
        $coords = self::randomCoords();
        $address = self::randomAddress();

        return new ScooterLocation($coords, $address);
    }

    public static function randomCoords(): CoordsValueObject
    {
        $faker = Factory::create();
        return new CoordsValueObject($faker->latitude(), $faker->longitude());
    }

    public static function randomAddress(): PlaceLocationValueObject
    {
        $faker = Factory::create();
        return new PlaceLocationValueObject($faker->streetAddress(), $faker->country(), $faker->state(), $faker->city(), $faker->postcode());
    }
}
