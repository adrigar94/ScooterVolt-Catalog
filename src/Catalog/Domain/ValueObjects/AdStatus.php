<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumValueObject;

class AdStatus extends EnumValueObject
{
    final public const DRAFT = 'draft';
    final public const PUBLISHED = 'published';
    final public const SOLD = 'sold';


    protected function valueMapping(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SOLD => 'Sold',
        ];
    }
}
