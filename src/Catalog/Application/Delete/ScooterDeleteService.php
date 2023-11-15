<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Application\Delete;

use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ScooterDeleteService
{
    public function __construct(
        private readonly ScooterRepository $repository,
        private readonly JwtDecoder $jwtDecoder
    ) {
    }

    public function __invoke(AdId $id): void
    {
        $this->checkPermissions($id);

        $this->repository->delete($id);
    }

    private function checkPermissions(AdId $id): void
    {
        $roles = $this->jwtDecoder->roles();
        if (in_array('ROLE_ADMIN', $roles)) {
            return;
        }

        $userIdLogged = $this->jwtDecoder->id();

        $scooterToDelete = $this->repository->findById($id);

        if (is_null($scooterToDelete)) {
            return;
        }

        if ($scooterToDelete->getUserId()->value() !== $userIdLogged) {
            throw new UnauthorizedHttpException('You do not have permission to delete this Scooter');
        }
    }
}
