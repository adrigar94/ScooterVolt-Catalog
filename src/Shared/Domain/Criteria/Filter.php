<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

class Filter
{
    public function __construct(
        private readonly string $field,
        private readonly FilterOperator $operator,
        private readonly string|int|float|bool $value
    ) {
    }

    public function field(): string
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): string|int|float|bool
    {
        return $this->value;
    }
}
