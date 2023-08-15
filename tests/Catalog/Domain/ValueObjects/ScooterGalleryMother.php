<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Domain\Images\ImageValueObject;
use Faker\Factory;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterGallery;

class ScooterGalleryMother
{

    public static function create(ImageValueObject ...$images): ScooterGallery
    {
        return new ScooterGallery(...$images);
    }


    public static function random($minImages = 1, $maxImages = 5): ScooterGallery
    {
        $images = [];
        for ($i = 0; $i < rand($minImages, $maxImages); $i++) {
            $images[] = self::randomImage();
        }
        return new ScooterGallery(...$images);
    }

    public static function randomImage(): ImageValueObject
    {
        $faker = Factory::create();

        $url = $faker->url();
        $alt = $faker->words(3, true);

        return ImageValueObject::create($url, $alt);
    }
}
