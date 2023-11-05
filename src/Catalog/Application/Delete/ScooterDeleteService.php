<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Delete;

use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;

class ScooterDeleteService
{
    public function __construct(
        private ScooterRepository $repository
    ) {
    }

    public function __invoke(AdId $id): void
    {
        $this->repository->delete($id);
    }
}
