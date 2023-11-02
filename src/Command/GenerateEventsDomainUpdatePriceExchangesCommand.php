<?php

namespace ScooterVolt\CatalogService\Command;

use ScooterVolt\CatalogService\Catalog\Domain\Events\ScooterUpdatePriceExchangeEvent;
use ScooterVolt\CatalogService\Catalog\Domain\ScooterRepository;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'event-domain:scooter:generate-events-update-price-exchanges',
    description: 'Generate ScooterUpdatePriceExchangeEvent events for all scooters. That update price conversion of all scooters',
)]
class GenerateEventsDomainUpdatePriceExchangesCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private ScooterRepository $repository,
        private EventBus $eventBus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scooters = $this->repository->findAll();

        foreach ($scooters as $scooter) {
            $event = ScooterUpdatePriceExchangeEvent::create($scooter->getId());
            $this->eventBus->publish($event);
        }
        return Command::SUCCESS;
    }

}