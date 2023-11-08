<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumValueObject;

class ScooterCondition extends EnumValueObject
{

    final public const NEW = 'new';
    final public const USED = 'used';
    final public const BROKEN = 'broken';


    protected function valueMapping(): array
    {
        return [
            self::NEW => 'New',
            self::USED => 'Used',
            self::BROKEN => 'Broken'
        ];
    }
}
