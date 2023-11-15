<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\UserId;

class UserIdMother
{
    public static function create(string $value): UserId
    {
        return new UserId($value);
    }

    public static function random(): UserId
    {
        return UserId::random();
    }
}
