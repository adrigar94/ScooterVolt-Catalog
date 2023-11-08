<?php

namespace ScooterVolt\CatalogService\Command;

use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for commands that listen for a domain event.
 */
abstract class EventDomainReadCommand extends Command
{
    protected int $maxEventReads = 10;

    /**
     * must be an extended class of DomainEvent.
     */
    abstract protected function domainEventClass(): string;

    abstract protected function queue(): string;

    abstract protected function handleEvent(DomainEvent $event): void;

    public function __construct(
        protected ScooterRepository $repository,
        protected EventBus $eventBus
    ) {
        if (! is_subclass_of($this->domainEventClass(), DomainEvent::class)) {
            throw new \InvalidArgumentException('$domainEventClass must be an extended class of DomainEvent');
        }

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callbackEvent = function ($message) {
            $event = $this->domainEventClass()::fromString($message->body);
            $this->handleEvent($event);
        };

        $this->eventBus->consume(
            $this->queue(),
            $this->domainEventClass()::eventName(),
            $callbackEvent,
            $this->maxEventReads
        );

        return Command::SUCCESS;
    }
}
