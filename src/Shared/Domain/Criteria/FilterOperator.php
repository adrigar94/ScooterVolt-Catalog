<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

use InvalidArgumentException;
use ReflectionClass;

class FilterOperator
{
    public const EQUAL        = '=';
    public const NOT_EQUAL    = '!=';
    public const GT           = '>';
    public const LT           = '<';
    public const CONTAINS     = 'CONTAINS';
    public const NOT_CONTAINS = 'NOT_CONTAINS';
    private static array $containing = [self::CONTAINS, self::NOT_CONTAINS];


    public function __construct(protected string $value)
    {
        $this->ensureIsBetweenAcceptedValues($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        $class = static::class;
        $reflected = new ReflectionClass($class);
        return $reflected->getConstants();
    }

    public static function __callStatic(string $name, $args)
    {
        return new static(self::values()[$name]);
    }

    private function ensureIsBetweenAcceptedValues($value): void
    {
        if (!in_array($value, static::values(), true)) {
            $this->throwExceptionForInvalidValue($value);
        }
    }


    public function isContaining(): bool
    {
        return in_array($this->value(), self::$containing, true);
    }

    protected function throwExceptionForInvalidValue($value): never
    {
        throw new InvalidArgumentException(sprintf('The filter <%s> is invalid', $value));
    }
}
