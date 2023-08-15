<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Domain\ValueObjects;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterCondition;

class ScooterConditionMother
{
    public static function create(string $value): ScooterCondition
    {
        return new ScooterCondition($value);
    }

    public static function createNew(): ScooterCondition
    {
        return new ScooterCondition(ScooterCondition::NEW);
    }

    public static function createUsed(): ScooterCondition
    {
        return new ScooterCondition(ScooterCondition::USED);
    }

    public static function createBroken(): ScooterCondition
    {
        return new ScooterCondition(ScooterCondition::BROKEN);
    }

    public static function random(): ScooterCondition
    {
        $conditions = [
            ScooterCondition::NEW,
            ScooterCondition::USED,
            ScooterCondition::BROKEN,
        ];

        return new ScooterCondition($conditions[array_rand($conditions)]);
    }
}
