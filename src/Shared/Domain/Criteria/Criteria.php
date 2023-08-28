<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

final class Criteria
{
    /**
     * @param array<Filter> $filters
     * @param array<Order> $order
     * @param int|null $offset
     * @param int|null $limit
     */
    public function __construct(
        private readonly array $filters,
        private readonly array $order = [],
        private readonly ?int $offset = null,
        private readonly ?int $limit = null
    ) {
    }

    public function hasFilters(): bool
    {
        return count($this->filters) > 0;
    }

    public function hasOrder(): bool
    {
        return count($this->order) > 0;
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
