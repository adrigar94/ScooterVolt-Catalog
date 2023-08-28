<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\Exceptions\ScooterSoldChangeStatusException;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterBrand;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterCondition;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterDescription;
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
    //TODO add price fields converted to each available currency type

    public function __construct(
        AdId $id,
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
        private ?ScooterPowerWatts $power = null,
        private ?ScooterDescription $description = null,
    ) {
        parent::__construct($id, $url, $createdAt, $updatedAt, $status, $user_id, $contactInfo);
    }

    public function getTitle(): string
    {
        return $this->brand->value() . ' ' . $this->model->value();
    }

    public static function newBlankScooter(UserId $user_id, UserContactInfo $contactInfo): self
    {
        $id = AdId::random();
        $url = AdUrl::generateRandomUrlForBlankAd();
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();
        $status = new AdStatus(AdStatus::DRAFT);
        return new static($id, $url, $createdAt, $updatedAt, $status, $user_id, $contactInfo);
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

    public function getDescription(): ?ScooterDescription
    {
        return $this->description;
    }

    public function setDescription(ScooterDescription $description): void
    {
        $this->description = $description;
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


    public function toNative(): array
    {

        $native = [
            'id' => $this->getId()->toNative(),
            'url' => $this->getUrl()->toNative(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'status' => $this->getStatus()->toNative(),
            'user_id' => $this->getUserId()->toNative(),
            'user_contact_info' => $this->getUserContactInfo()->toNative(),
        ];

        if ($this->getBrand()) {
            $native['brand'] = $this->getBrand()->toNative();
        }
        if ($this->getModel()) {
            $native['model'] = $this->getModel()->toNative();
        }
        if ($this->getPrice()) {
            $native['price'] = $this->getPrice()->toNative();
        }
        if ($this->getLocation()) {
            $native['location'] = $this->getLocation()->toNative();
        }
        if ($this->getGallery()) {
            $native['gallery'] = $this->getGallery()->toNative();
        }
        if ($this->getYear()) {
            $native['year'] = $this->getYear()->toNative();
        }
        if ($this->getCondition()) {
            $native['condition'] = $this->getCondition()->toNative();
        }
        if ($this->getTravelRange()) {
            $native['travel_range'] = $this->getTravelRange()->toNative();
        }
        if ($this->getMaxSpeed()) {
            $native['max_speed'] = $this->getMaxSpeed()->toNative();
        }
        if ($this->getPower()) {
            $native['power'] = $this->getPower()->toNative();
        }
        if ($this->getDescription()) {
            $native['description'] = $this->getDescription()->toNative();
        }

        return $native;
    }

    public static function fromNative(array $native): self
    {

        $scooter = new static(
            AdId::fromNative($native['id']),
            AdUrl::fromNative($native['url']),
            new \DateTimeImmutable($native['created_at']),
            new \DateTimeImmutable($native['updated_at']),
            AdStatus::fromNative($native['status']),
            UserId::fromNative($native['user_id']),
            UserContactInfo::fromNative($native['user_contact_info'])
        );

        if (array_key_exists('brand', $native) and !is_null($native['brand'])) {
            $scooter->setBrand(ScooterBrand::fromNative($native['brand']));
        }
        if (array_key_exists('model', $native) and !is_null($native['model'])) {
            $scooter->setModel(ScooterModel::fromNative($native['model']));
        }
        if (array_key_exists('price', $native) and !is_null($native['price'])) {
            $scooter->setPrice(ScooterPrice::fromNative($native['price']));
        }
        if (array_key_exists('location', $native) and !is_null($native['location'])) {
            $scooter->setLocation(ScooterLocation::fromNative($native['location']));
        }
        if (array_key_exists('gallery', $native) and !is_null($native['gallery'])) {
            $scooter->setGallery(ScooterGallery::fromNative($native['gallery']));
        }
        if (array_key_exists('year', $native) and !is_null($native['year'])) {
            $scooter->setYear(ScooterYear::fromNative($native['year']));
        }
        if (array_key_exists('condition', $native) and !is_null($native['condition'])) {
            $scooter->setCondition(ScooterCondition::fromNative($native['condition']));
        }
        if (array_key_exists('travel_range', $native) and !is_null($native['travel_range'])) {
            $scooter->setTravelRange(ScooterTravelRangeKm::fromNative($native['travel_range']));
        }
        if (array_key_exists('max_speed', $native) and !is_null($native['max_speed'])) {
            $scooter->setMaxSpeed(ScooterMaxSpeedKmh::fromNative($native['max_speed']));
        }
        if (array_key_exists('power', $native) and !is_null($native['power'])) {
            $scooter->setPower(ScooterPowerWatts::fromNative($native['power']));
        }
        if (array_key_exists('description', $native) and !is_null($native['description'])) {
            $scooter->setDescription(ScooterDescription::fromNative($native['description']));
        }

        return $scooter;
    }

    public function jsonSerialize(): array
    {
        return $this->toNative();
    }
}
