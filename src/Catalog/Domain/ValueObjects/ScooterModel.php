<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\String\StringValueObject;

class ScooterModel extends StringValueObject
{

    protected static function getMinLength(): int
    {
        return 3;
    }

    protected static function getMaxLength(): int
    {
        return 70;
    }

}