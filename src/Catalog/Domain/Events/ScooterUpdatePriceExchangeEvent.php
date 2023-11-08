<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\Events;

use DateTimeImmutable;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\AdId;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;

class ScooterUpdatePriceExchangeEvent extends DomainEvent
{

    /**
     * @param array $scooterId ['scooter_id' => string]
     */
    public function __construct(
        array $scooterId,
        ?string $eventId = null,
        ?DateTimeImmutable $occurredOn = null
    ) {
        $aggregateId = $scooterId['scooter_id'];
        parent::__construct($aggregateId, $scooterId, $eventId, $occurredOn);
    }

    public static function create(AdId $scooterId): self
    {
        return new static(
            ['scooter_id' => $scooterId->value()]
        );
    }

    public static function eventName(): string
    {
        return 'scooter.update-price-exchange';
    }

    public function toPrimitives(): array
    {
        return [
            'aggregateId' => $this->aggregateId(),
            'body' => $this->body(),
            'eventId' => $this->eventId(),
            'occurredOn' => serialize($this->occurredOn()),
        ];
    }

    public static function fromPrimitives(string $aggregateId, array $scooterId, ?string $eventId, ?DateTimeImmutable $occurredOn): self
    {
        return new static(
            $scooterId,
            $eventId,
            $occurredOn
        );
    }

    public static function fromString(string $event): self
    {
        $eventObject = json_decode($event, true, 512, JSON_THROW_ON_ERROR);

        return self::fromPrimitives(
            $eventObject['aggregateId'],
            $eventObject['body'],
            $eventObject['eventId'],
            $eventObject['occurredOn'] ? unserialize($eventObject['occurredOn']) : null
        );
    }

    public function getScooterId(): AdId
    {
        return AdId::fromNative($this->body()['scooter_id']);
    }
}