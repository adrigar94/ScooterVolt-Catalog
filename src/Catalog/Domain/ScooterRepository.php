<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain;

use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;

interface ScooterRepository
{
    /**
     * @return Scooter[]
     */
    public function findAll(): array;


    /**
     * @return Scooter[]
     */
    public function search(Criteria $criteria): array;

    public function findById(AdId $id): ?Scooter;

    public function findByUrl(AdUrl $url): ?Scooter;

    public function save(Scooter $scooter): void;

    public function delete(AdId $id): void;
}
