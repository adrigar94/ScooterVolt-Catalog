<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Infrastructure\Persistence;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdUrl;
use ScooterVolt\CatalogService\Shared\Domain\Criteria\Criteria;

final class DoctrineScooterRepository implements ScooterRepository
{
    private const TABLE_NAME = 'scooters';

    public function __construct(private Connection $connection)
    {
    }

    public function findAll(): array
    {
        //TODO
        return [];
    }

    public function search(Criteria $criteria): array
    {
        //TODO
        return [];
    }

    public function findById(AdId $id): ?Scooter
    {
        //TODO
        return null;
    }

    public function findByUrl(AdUrl $url): ?Scooter
    {
        //TODO
        return null;
    }

    public function save(Scooter $user): void
    {
        //TODO
    }

    public function delete(AdId $id): void
    {
        //TODO
    }
}
