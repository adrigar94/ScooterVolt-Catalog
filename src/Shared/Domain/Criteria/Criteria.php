<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Criteria;

final class Criteria
{
    // TODO review good example https://github.com/CodelyTV/php-ddd-example/tree/main/src/Shared/Domain/Criteria
    public function __construct(
        private readonly array $filters, //TODO create tpyed var
        private readonly array $order, //TODO create tpyed var
        private readonly ?int $offset,
        private readonly ?int $limit
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

    public function filters(): array
    {
        return $this->filters;
    }

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
}
