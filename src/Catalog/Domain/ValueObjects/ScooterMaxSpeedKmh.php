<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\Int\IntValueObject;

class ScooterMaxSpeedKmh extends IntValueObject
{
    protected static function getMinValue(): int
    {
        return 0;
    }

    protected static function getMaxValue(): int
    {
        return 300;
    }

    public function __toString(): string
    {
        return $this->value . ' km/h';
    }
}
