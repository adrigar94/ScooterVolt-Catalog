<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Bus\Event;

interface DomainEventSubscriber
{
    public function subscribedTo(): array;
}