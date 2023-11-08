<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Domain\Bus\Event;

interface EventBus
{
    public function publish(DomainEvent ...$events): void;

    public function consume(string $queue, string $eventName, \Closure $callbackEvent, int $maxEventReads): void;
}
