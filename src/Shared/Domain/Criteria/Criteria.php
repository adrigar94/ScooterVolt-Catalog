<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

final readonly class Criteria
{
    /**
     * @param array<Filter> $filters
     * @param array<Order> $order
     * @param int|null $offset
     * @param int|null $limit
     */
    public function __construct(
        private array $filters,
        private array $order = [],
        private ?int $offset = null,
        private ?int $limit = null
    ) {
    }

    public function hasFilters(): bool
    {
        return $this->filters !== [];
    }

    public function hasOrder(): bool
    {
        return $this->order !== [];
    }

    /**
     * @return array<Filter>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<Order>
     */
    public function order(): array
    {
        return $this->order;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function hasLimit(): bool
    {
        return !is_null($this->limit());
    }

    public function hasOffset(): bool
    {
        return !is_null($this->offset());
    }
}
