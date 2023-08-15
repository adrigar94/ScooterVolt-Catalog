<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserContactInfo;

class UserContactInfoMother
{

    public static function create(string $name, string $phone, string $email): UserContactInfo
    {
        return new UserContactInfo($name, $phone, $email);
    }


    public static function random(): UserContactInfo
    {
        $faker = Factory::create();
        $name = $faker->name();
        $phone = $faker->phoneNumber();
        $email = $faker->email();

        return new UserContactInfo($name, $phone, $email);
    }
}
