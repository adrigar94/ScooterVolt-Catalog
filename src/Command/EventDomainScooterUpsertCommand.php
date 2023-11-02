<?php

namespace ScooterVolt\CatalogService\Command;

use ScooterVolt\CatalogService\Catalog\Domain\Events\ScooterUpdatePriceExchangeEvent;
use ScooterVolt\CatalogService\Catalog\Domain\Events\ScooterUpsertDomainEvent;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'event-domain:scooter:upsert',
    description: 'Read scooter.upsert events',
)]
class EventDomainScooterUpsertCommand extends EventDomainReadCommand
{
    protected function domainEventClass(): string
    {
        return ScooterUpsertDomainEvent::class;
    }
    protected function queue(): string
    {
        return 'Scooter_Upsert';
    }

    protected function handleEvent(DomainEvent $event): void
    {
        if (!$event instanceof ScooterUpsertDomainEvent) {
            throw new \InvalidArgumentException('Event is not a ScooterUpsertDomainEvent');
        }

        $scooter = $event->getScooter();

        $this->generateUpdatePriceExchangeEvent($scooter);
    }

    private function generateUpdatePriceExchangeEvent(Scooter $scooter): void
    {
        $event = ScooterUpdatePriceExchangeEvent::create($scooter->getId());
        $this->eventBus->publish($event);
    }
}