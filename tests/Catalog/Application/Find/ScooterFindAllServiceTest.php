<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Find;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Find\ScooterFindAllService;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ScooterFindAllServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;

    private JwtDecoder|MockObject $jwtDecoder;


    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
        $this->jwtDecoder = $this->createMock(JwtDecoder::class);
    }

    public function testFindAllReturnsScooters(): void
    {
        $scooters = [
            ScooterMother::randomPublished(),
            ScooterMother::randomPublished(),
        ];
        $this->repositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn($scooters);


        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_ADMIN']);

        $service = new ScooterFindAllService($this->repositoryMock, $this->jwtDecoder);

        $result = $service->__invoke();

        $this->assertSame($scooters, $result);
    }

    public function testFindAllUnauthorized(): void
    {
        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_USER']);

        $service = new ScooterFindAllService($this->repositoryMock, $this->jwtDecoder);

        $this->expectException(UnauthorizedHttpException::class);
        $service->__invoke();
    }
}
