<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserContactInfo;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserId;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\AdStatusMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterBrandMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterConditionMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterGalleryMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterLocationMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterMaxSpeedKmhMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterModelMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterPowerWattsMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterPriceMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterTravelRangeKmMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterYearMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\UserContactInfoMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\UserIdMother;

class ScooterMother
{

    public static function newBlankScooter(UserId $userId = null, UserContactInfo $contactInfo = null): Scooter
    {
        if (!$userId) {
            $userId = UserIdMother::random();
        }
        if (!$contactInfo) {
            $contactInfo = UserContactInfoMother::random();
        }

        return new Scooter(
            AdUrl::generateRandomUrlForBlankAd(),
            new \DateTimeImmutable,
            new \DateTimeImmutable,
            AdStatusMother::createDraft(),
            $userId,
            $contactInfo
        );
    }

    public static function random(AdStatus $status = null): Scooter
    {
        if (!$status) {
            $status = AdStatusMother::random();
        }

        $scooter = new Scooter(
            AdUrl::generateRandomUrlForBlankAd(),
            new \DateTimeImmutable,
            new \DateTimeImmutable,
            $status,
            UserIdMother::random(),
            UserContactInfoMother::random()
        );

        $brand       = ScooterBrandMother::random();
        $model       = ScooterModelMother::random();
        $price       = ScooterPriceMother::random();
        $location    = ScooterLocationMother::random();
        $gallery     = ScooterGalleryMother::random();
        $year        = ScooterYearMother::random();
        $condition   = ScooterConditionMother::random();
        $travelRange = ScooterTravelRangeKmMother::random();
        $maxSpeed    = ScooterMaxSpeedKmhMother::random();
        $power       = ScooterPowerWattsMother::random();

        $scooter->setBrand($brand);
        $scooter->setModel($model);
        $scooter->setPrice($price);
        $scooter->setLocation($location);
        $scooter->setGallery($gallery);
        $scooter->setYear($year);
        $scooter->setCondition($condition);
        $scooter->setTravelRange($travelRange);
        $scooter->setMaxSpeed($maxSpeed);
        $scooter->setPower($power);

        return $scooter;
    }

    public static function randomDraft(): Scooter
    {
        return self::random(AdStatusMother::createDraft());
    }

    public static function randomPublished(): Scooter
    {
        return self::random(AdStatusMother::createPublished());
    }

    public static function randomSold(): Scooter
    {
        return self::random(AdStatusMother::createSold());
    }
}
