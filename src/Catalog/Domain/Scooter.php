<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterSoldChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterBrand;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterCondition;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterGallery;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterLocation;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterMaxSpeedKmh;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterModel;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterPowerWatts;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterPrice;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterTravelRangeKm;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterYear;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserContactInfo;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserId;

final class Scooter extends Ad
{

    public function __construct(
        AdUrl $url,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        AdStatus $status,
        UserId $user_id,
        UserContactInfo $contactInfo,
        private ?ScooterBrand $brand = null,
        private ?ScooterModel $model = null,
        private ?ScooterPrice $price = null,
        private ?ScooterLocation $location = null,
        private ?ScooterGallery $gallery = null,
        private ?ScooterYear $year = null,
        private ?ScooterCondition $condition = null,
        private ?ScooterTravelRangeKm $travelRange = null,
        private ?ScooterMaxSpeedKmh $maxSpeed = null,
        private ?ScooterPowerWatts $power = null
    ) {
        parent::__construct($url, $createdAt, $updatedAt, $status, $user_id, $contactInfo);
    }

    public function getTitle(): string
    {
        return $this->brand->value() . ' ' . $this->model->value();
    }

    public static function newBlankScooter(UserId $user_id, UserContactInfo $contactInfo): self
    {
        $url = AdUrl::generateRandomUrlForBlankAd();
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();
        $status = new AdStatus(AdStatus::DRAFT);
        return new static($url, $createdAt, $updatedAt, $status, $user_id, $contactInfo);
    }

    public function getBrand(): ?ScooterBrand
    {
        return $this->brand;
    }

    public function setBrand(ScooterBrand $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): ?ScooterModel
    {
        return $this->model;
    }

    public function setModel(ScooterModel $model): void
    {
        $this->model = $model;
    }

    public function getPrice(): ?ScooterPrice
    {
        return $this->price;
    }

    public function setPrice(ScooterPrice $price): void
    {
        $this->price = $price;
    }

    public function getLocation(): ?ScooterLocation
    {
        return $this->location;
    }

    public function setLocation(ScooterLocation $location): void
    {
        $this->location = $location;
    }

    public function getGallery(): ?ScooterGallery
    {
        return $this->gallery;
    }

    public function setGallery(ScooterGallery $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getYear(): ?ScooterYear
    {
        return $this->year;
    }

    public function setYear(ScooterYear  $year): void
    {
        $this->year = $year;
    }

    public function getCondition(): ?ScooterCondition
    {
        return $this->condition;
    }

    public function setCondition(ScooterCondition $condition): void
    {
        $this->condition = $condition;
    }

    public function getTravelRange(): ?ScooterTravelRangeKm
    {
        return $this->travelRange;
    }

    public function setTravelRange(ScooterTravelRangeKm $travelRange): void
    {
        $this->travelRange = $travelRange;
    }

    public function getMaxSpeed(): ?ScooterMaxSpeedKmh
    {
        return $this->maxSpeed;
    }

    public function setMaxSpeed(ScooterMaxSpeedKmh $maxSpeed): void
    {
        $this->maxSpeed = $maxSpeed;
    }

    public function getPower(): ?ScooterPowerWatts
    {
        return $this->power;
    }

    public function setPower(ScooterPowerWatts $power): void
    {
        $this->power = $power;
    }

    protected function toDraftValidations(): void
    {
        if ($this->getStatus()->value() === AdStatus::SOLD) {
            throw new ScooterSoldChangeStatusException($this->getStatus()->value());
        }
    }

    protected function toPublishValidations(): void
    {
        if ($this->getStatus()->value() === AdStatus::SOLD) {
            throw new ScooterSoldChangeStatusException($this->getStatus()->value());
        }

        if (is_null($this->getBrand())) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Brand is required');
        }

        if (is_null($this->getModel())) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Model is required');
        }

        if (is_null($this->getPrice())) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Price is required');
        }

        if (is_null($this->getLocation())) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Location is required');
        }

        if (is_null($this->getGallery())) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Gallery is required');
        }

        if (count($this->getGallery()->getImages()) < 1) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::PUBLISHED, 'Gallery must have at least 1 image');
        }
    }

    protected function toSoldValidations(): void
    {
        if ($this->getStatus()->value() !== AdStatus::PUBLISHED) {
            throw new ScooterChangeStatusException($this->getStatus()->value(), AdStatus::SOLD, 'Only the published ad can be changed to sold');
        }
    }

    protected function afterPublish(): void
    {
        $this->setUrl(
            $this->getUrl()->generateUrlFromTitle($this->getTitle())
        );
    }
}
