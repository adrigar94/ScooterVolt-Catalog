<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\Enum\EnumValueObject;

class AdStatus extends EnumValueObject
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const SOLD = 'sold';


    protected function valueMapping(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SOLD => 'Sold',
        ];
    }
}
