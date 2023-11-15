<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Delete;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Delete\ScooterDeleteService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ScooterDeleteServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;

    private JwtDecoder|MockObject $jwtDecoder;


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
        $this->jwtDecoder = $this->createMock(JwtDecoder::class);
    }

    public function testDeleteService(): void
    {
        $scooter = ScooterMother::randomPublished();

        $this->repositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($scooter);

        $this->repositoryMock->expects($this->once())
            ->method('delete');

        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_USER']);

        $this->jwtDecoder->expects($this->once())
            ->method('id')
            ->willReturn($scooter->getUserId()->value());

        $service = new ScooterDeleteService($this->repositoryMock, $this->jwtDecoder);

        $service->__invoke($scooter->getId());
    }


    public function testDeleteUnauthorized(): void
    {
        $scooter = ScooterMother::randomPublished();

        $this->repositoryMock->expects($this->once())
            ->method('findById')
            ->willReturn($scooter);

        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_USER']);

        $this->jwtDecoder->expects($this->once())
            ->method('id')
            ->willReturn("other-user");

        $service = new ScooterDeleteService($this->repositoryMock, $this->jwtDecoder);

        $this->expectException(UnauthorizedHttpException::class);
        $service->__invoke($scooter->getId());
    }
}
