<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Domain\Currency\CurrencyValueObject;
use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterPrice;

class ScooterPriceMother
{
    public static function create(int $price, CurrencyValueObject $currency): ScooterPrice
    {
        return ScooterPrice::createPrice($price, $currency);
    }

    public static function random(): ScooterPrice
    {
        $faker = Factory::create();

        $price = $faker->numberBetween(1, 1000);
        $currency = self::randomCurrency();

        return ScooterPrice::createPrice($price, $currency);
    }

    public static function randomCurrency(): CurrencyValueObject
    {
        $oClass = new \ReflectionClass(CurrencyValueObject::class);
        $constants = $oClass->getConstants();

        return new CurrencyValueObject($constants[array_rand($constants)]);
    }
}
