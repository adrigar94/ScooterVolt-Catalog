<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

class Order
{
    public function __construct(
        private readonly string $orderBy,
        private readonly OrderType $orderType
    ) {
    }

    public function orderBy(): string
    {
        return $this->orderBy;
    }

    public function orderType(): OrderType
    {
        return $this->orderType;
    }
}
