<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

use InvalidArgumentException;
use ReflectionClass;

class OrderType
{
    public const ASC  = 'asc';
    public const DESC = 'desc';
    public const NONE = 'none';


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

    protected function throwExceptionForInvalidValue($value): never
    {
        throw new InvalidArgumentException(sprintf('The order type <%s> is invalid', $value));
    }
}
