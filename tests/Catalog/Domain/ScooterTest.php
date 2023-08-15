<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterSoldChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
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

#[CoversClass(Scooter::class)]
class ScooterTest extends TestCase
{

    public function testCreateScooterInstance()
    {
        $scooter = ScooterMother::random();
        $this->assertInstanceOf(Scooter::class, $scooter);
    }

    public function testCreateScooterAndGetAttributesInstance()
    {
        $userId = UserIdMother::random();
        $contactInfo = UserContactInfoMother::random();

        $scooter = Scooter::newBlankScooter($userId, $contactInfo);


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


        $this->assertInstanceOf(Scooter::class, $scooter);

        $this->assertSame($brand, $scooter->getBrand());
        $this->assertSame($model, $scooter->getModel());
        $this->assertSame($price, $scooter->getPrice());
        $this->assertSame($location, $scooter->getLocation());
        $this->assertSame($gallery, $scooter->getGallery());
        $this->assertSame($year, $scooter->getYear());
        $this->assertSame($condition, $scooter->getCondition());
        $this->assertSame($travelRange, $scooter->getTravelRange());
        $this->assertSame($maxSpeed, $scooter->getMaxSpeed());
        $this->assertSame($power, $scooter->getPower());
    }


    public function testDraftToPublish(): void
    {
        $scooter = ScooterMother::randomDraft();

        $oldUrl = $scooter->getUrl()->value();
        $scooter->toPublish();
        $newUrl = $scooter->getUrl()->value();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::PUBLISHED, $scooter->getStatus()->value());
        $this->assertNotSame($oldUrl, $newUrl);
    }

    public function testDraftToPublishFailed(): void
    {
        // TODO check all verifications toPublish
        $scooter = ScooterMother::newBlankScooter();

        $this->expectException(ScooterChangeStatusException::class);
        $scooter->toPublish();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::DRAFT, $scooter->getStatus());
    }

    public function testDraftToSoldFailed(): void
    {
        $scooter = ScooterMother::randomDraft();
        $this->expectException(ScooterChangeStatusException::class);
        $scooter->toSold();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::DRAFT, $scooter->getStatus());
    }

    public function testPublishToDraft(): void
    {
        $scooter = ScooterMother::randomPublished();
        $scooter->toDraft();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::DRAFT, $scooter->getStatus()->value());
    }

    public function testPublishToSold(): void
    {
        $scooter = ScooterMother::randomPublished();
        $scooter->toSold();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::SOLD, $scooter->getStatus()->value());
    }

    public function testSoldToDraftFailed(): void
    {
        $scooter = ScooterMother::randomSold();


        $this->expectException(ScooterSoldChangeStatusException::class);
        $scooter->toDraft();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::SOLD, $scooter->getStatus());
    }


    public function testSoldToPublishFailed(): void
    {
        $scooter = ScooterMother::randomSold();

        $this->expectException(ScooterSoldChangeStatusException::class);
        $scooter->toPublish();

        $this->assertInstanceOf(Scooter::class, $scooter);
        $this->assertSame(AdStatus::SOLD, $scooter->getStatus());
    }
}
