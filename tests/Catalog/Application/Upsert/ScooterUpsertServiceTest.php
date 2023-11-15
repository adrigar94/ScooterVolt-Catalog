<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Catalog\Application\Upsert;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Catalog\Application\Upsert\ScooterUpsertService;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Auth\JwtDecoder;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;
use ScooterVolt\CatalogService\Tests\Catalog\Domain\ScooterMother;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ScooterUpsertServiceTest extends TestCase
{
    private ScooterRepository|MockObject $repositoryMock;
    private EventBus|MockObject $eventBus;

    private JwtDecoder|MockObject $jwtDecoder;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ScooterRepository::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->jwtDecoder = $this->createMock(JwtDecoder::class);
    }

    public function testScooterUpsertService(): void
    {
        $scooterDTO = ScooterMother::randomScooterDTO();

        $this->repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Scooter::class));

        $this->eventBus->expects($this->once())
            ->method('publish');

        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_USER']);

        $this->jwtDecoder->expects($this->once())
            ->method('id')
            ->willReturn($scooterDTO->user_id);

        $service = new ScooterUpsertService($this->repositoryMock, $this->eventBus, $this->jwtDecoder);

        $service->__invoke($scooterDTO);
    }


    public function testUpsertUnauthorized(): void
    {
        $scooterDTO = ScooterMother::randomScooterDTO();

        $this->jwtDecoder->expects($this->once())
            ->method('roles')
            ->willReturn(['ROLE_USER']);


        $this->jwtDecoder->expects($this->once())
            ->method('id')
            ->willReturn("other-user");

        $service = new ScooterUpsertService($this->repositoryMock, $this->eventBus, $this->jwtDecoder);

        $this->expectException(UnauthorizedHttpException::class);
        $service->__invoke($scooterDTO);

    }
}