<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Upsert;

use Adrigar94\ValueObjectCraft\Domain\Currency\CurrencyValueObject;
use Adrigar94\ValueObjectCraft\Domain\Location\CoordsValueObject;
use Adrigar94\ValueObjectCraft\Domain\Location\PlaceLocationValueObject;
use ScooterVolt\CatalogService\Catalog\Domain\Events\ScooterUpsertDomainEvent;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterDTO;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
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
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;

class ScooterUpsertService
{
    public function __construct(
        private readonly ScooterRepository $repository,
        private readonly EventBus $eventBus
    ) {
    }

    public function __invoke(ScooterDTO $scooterDTO): void
    {
        $id = new AdId($scooterDTO->id);
        $url = new AdUrl($scooterDTO->url);
        $createdAt = new \DateTimeImmutable($scooterDTO->created_at);
        $updatedAt = new \DateTimeImmutable($scooterDTO->updated_at);
        $status = new AdStatus($scooterDTO->status);
        $userId = new UserId($scooterDTO->user_id);
        $contactInfo = new UserContactInfo($scooterDTO->user_contact_info['name'], $scooterDTO->user_contact_info['phone'], $scooterDTO->user_contact_info['email']);

        $scooter = new Scooter($id, $url, $createdAt, $updatedAt, $status, $userId, $contactInfo);

        if ($scooterDTO->brand) {
            $scooter->setBrand(new ScooterBrand($scooterDTO->brand));
        }
        if ($scooterDTO->model) {
            $scooter->setModel(new ScooterModel($scooterDTO->model));
        }
        if ($scooterDTO->price) {
            $scooter->setPrice(ScooterPrice::createPrice((float) $scooterDTO->price['price'], new CurrencyValueObject($scooterDTO->price['currency'])));
        }
        if ($scooterDTO->location) {
            $coords = new CoordsValueObject((float) $scooterDTO->location['coords']['coordinates'][0], (float) $scooterDTO->location['coords']['coordinates'][1]);
            $place = new PlaceLocationValueObject(
                $scooterDTO->location['location']['locality'],
                $scooterDTO->location['location']['country'],
                array_key_exists('region', (array) $scooterDTO->location['location']) ? $scooterDTO->location['location']['region'] : null,
                array_key_exists('city', (array) $scooterDTO->location['location']) ? $scooterDTO->location['location']['city'] : null,
                array_key_exists('postalCod', (array) $scooterDTO->location['location']) ? $scooterDTO->location['location']['postalCod'] : null
            );
            $scooter->setLocation(new ScooterLocation($coords, $place));
        }
        if ($scooterDTO->gallery) {
            $scooter->setGallery(ScooterGallery::fromNative($scooterDTO->gallery));
        }
        if ($scooterDTO->year) {
            $scooter->setYear(new ScooterYear($scooterDTO->year));
        }
        if ($scooterDTO->condition) {
            $scooter->setCondition(new ScooterCondition($scooterDTO->condition));
        }
        if ($scooterDTO->travel_range) {
            $scooter->setTravelRange(new ScooterTravelRangeKm($scooterDTO->travel_range));
        }
        if ($scooterDTO->max_speed) {
            $scooter->setMaxSpeed(new ScooterMaxSpeedKmh($scooterDTO->max_speed));
        }
        if ($scooterDTO->power) {
            $scooter->setPower(new ScooterPowerWatts($scooterDTO->power));
        }
        if ($scooterDTO->description) {
            $scooter->setDescription(new ScooterDescription($scooterDTO->description));
        }

        $this->repository->save($scooter);

        $eventUpsert = new ScooterUpsertDomainEvent($scooter->toNative());
        $this->eventBus->publish($eventUpsert);
    }
}
