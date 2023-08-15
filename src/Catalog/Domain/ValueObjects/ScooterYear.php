<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\Int\IntValueObject;

class ScooterYear extends IntValueObject
{
    protected static function getMinValue(): int
    {
        return 2000;
    }

    protected static function getMaxValue(): int
    {
        return (int)date('Y');
    }
}
