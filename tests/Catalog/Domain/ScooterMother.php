<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserContactInfo;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserId;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\AdStatusMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterBrandMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterConditionMother;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects\ScooterDescriptionMother;
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
            AdId::random(),
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
            AdId::random(),
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
        $description = ScooterDescriptionMother::random();

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
        $scooter->setDescription($description);

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

    public static function randomScooterDTO(): ScooterDTO
    {
        $scooter = self::random();

        return new ScooterDTO(
            $scooter->getId()->toNative(),
            $scooter->getUrl()->toNative(),
            $scooter->getCreatedAt()->format('Y-m-d H:i:s'),
            $scooter->getUpdatedAt()->format('Y-m-d H:i:s'),
            $scooter->getStatus()->toNative(),
            $scooter->getUserId()->toNative(),
            $scooter->getContactInfo()->toNative(),
            $scooter->getBrand()       ? $scooter->getBrand()->toNative()       : null,
            $scooter->getModel()       ? $scooter->getModel()->toNative()       : null,
            $scooter->getPrice()       ? $scooter->getPrice()->toNative()       : null,
            $scooter->getLocation()    ? $scooter->getLocation()->toNative()    : null,
            $scooter->getGallery()     ? $scooter->getGallery()->toNative()     : null,
            $scooter->getYear()        ? $scooter->getYear()->toNative()        : null,
            $scooter->getCondition()   ? $scooter->getCondition()->toNative()   : null,
            $scooter->getTravelRange() ? $scooter->getTravelRange()->toNative() : null,
            $scooter->getMaxSpeed()    ? $scooter->getMaxSpeed()->toNative()    : null,
            $scooter->getPower()       ? $scooter->getPower()->toNative()       : null,
            $scooter->getDescription() ? $scooter->getDescription()->toNative() : null
        );
    }
}
