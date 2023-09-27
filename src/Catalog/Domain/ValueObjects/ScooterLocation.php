<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Domain\Location\CoordsValueObject;
use Adrigar94\ValueObjectCraft\Domain\Location\GeoLocationValueObject;
use Adrigar94\ValueObjectCraft\Domain\Location\PlaceLocationValueObject;
use InvalidArgumentException;

class ScooterLocation extends GeoLocationValueObject
{
    public static function fromNative($native)
    {
        if (!is_array($native)) {
            throw new InvalidArgumentException('Invalid native data provided.');
        }

        if (!isset($native['coords'], $native['location'])) {
            throw new InvalidArgumentException('Coords and location values are required.');
        }

        $coords = self::coordsValueObjectFromNative($native['coords']);
        $location = PlaceLocationValueObject::fromNative($native['location']);

        return new static($coords, $location);
    }

    public function toNative(): array
    {
        return [
            'coords' => [
                'type' => 'Point',
                'coordinates' => [
                    $this->getCoords()->getLongitude(),
                    $this->getCoords()->getLatitude()
                ],
            ],
            'location' => $this->getLocation()->toNative(),
        ];
    }

    static private function coordsValueObjectFromNative($native): CoordsValueObject
    {
        if (!is_array($native)) {
            throw new InvalidArgumentException('Invalid native data provided.');
        }

        if (!isset($native['coordinates'][0], $native['coordinates'][1])) {
            throw new InvalidArgumentException('Latitude and longitude values are required.');
        }

        return new CoordsValueObject((float) $native['coordinates'][1], (float) $native['coordinates'][0]);
    }
}