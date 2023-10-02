<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\Events;

use DateTimeImmutable;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;

class ScooterUpsertDomainEvent extends DomainEvent
{


    public function __construct(
        array $scooterNative,
        ?string $eventId = null,
        ?DateTimeImmutable $occurredOn = null
    ) {
        $aggregateId = $scooterNative['id'];
        parent::__construct($aggregateId, $scooterNative, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'scooter.upsert';
    }

    public function toPrimitives(): array
    {
        return [
            'aggregateId' => $this->aggregateId(),
            'body' => $this->body(),
            'eventId' => $this->eventId(),
            'occurredOn' => $this->occurredOn(),
        ];
    }

    public static function fromPrimitives(string $aggregateId, array $body, ?string $eventId, ?DateTimeImmutable $occurredOn): self
    {
        return new static(
            $body,
            $eventId,
            $occurredOn
        );
    }
}