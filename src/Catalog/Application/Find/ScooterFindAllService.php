<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Find;

use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ScooterFindAllService
{
    public function __construct(
        private readonly ScooterRepository $repository,
        private readonly JwtDecoder $jwtDecoder
    ) {
    }

    /**
     * @return Scooter[]
     */
    public function __invoke(): array
    {
        $this->checkPermissions();

        return $this->repository->findAll();
    }

    private function checkPermissions(): void
    {
        $roles = $this->jwtDecoder->roles();
        if (in_array('ROLE_ADMIN', $roles)) {
            return;
        }

        throw new UnauthorizedHttpException('You do not have permission to upsert this Scooter');
    }
}
