<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdStatus;

class AdStatusMother
{
    public static function create(string $value): AdStatus
    {
        return new AdStatus($value);
    }

    public static function createDraft(): AdStatus
    {
        return new AdStatus(AdStatus::DRAFT);
    }

    public static function createPublished(): AdStatus
    {
        return new AdStatus(AdStatus::PUBLISHED);
    }

    public static function createSold(): AdStatus
    {
        return new AdStatus(AdStatus::SOLD);
    }

    public static function random(): AdStatus
    {
        $conditions = [
            AdStatus::DRAFT,
            AdStatus::PUBLISHED,
            AdStatus::SOLD
        ];

        return new AdStatus($conditions[array_rand($conditions)]);
    }
}
