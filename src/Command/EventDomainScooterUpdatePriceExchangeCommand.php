<?php

namespace ScooterVolt\CatalogService\Command;

use ScooterVolt\CatalogService\Catalog\Domain\Events\ScooterUpdatePriceExchangeEvent;
use ScooterVolt\CatalogService\Catalog\Domain\Scooter;
use ScooterVolt\CatalogService\Catalog\Domain\ValueObjects\ScooterPrice;
use ScooterVolt\CatalogService\Shared\Application\CurrencyConversor;
use ScooterVolt\CatalogService\Shared\Domain\Bus\Event\DomainEvent;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'event-domain:scooter:update-price-exchange',
    description: 'Read scooter.update-price-exchange events for update price conversions',
)]
class EventDomainScooterUpdatePriceExchangeCommand extends EventDomainReadCommand
{
    private $currenciesToConvert = ['EUR', 'GBP', 'USD', 'JPY'];

    protected function domainEventClass(): string
    {
        return ScooterUpdatePriceExchangeEvent::class;
    }
    protected function queue(): string
    {
        return 'Scooter_UpdatePriceExchange';
    }

    protected function handleEvent(DomainEvent $event): void
    {
        if (!$event instanceof ScooterUpdatePriceExchangeEvent) {
            throw new \InvalidArgumentException('Event is not a ScooterUpdatePriceExchangeEvent');
        }

        $scooter_id = $event->getScooterId();
        $scooter = $this->repository->findById($scooter_id);

        $this->updatePricesRates($scooter);
    }

    private function updatePricesRates(Scooter $scooter): void
    {
        $price = $scooter->getPrice();
        if (!$price) {
            return;
        }
        $valuePrice = $price->getPrice();
        $currency = $price->getCurrency();
        $conversor = new CurrencyConversor($currency->value());

        $pricesConverted = [];
        foreach ($this->currenciesToConvert as $currencyToConvert) {
            $pricesConverted[$currencyToConvert] = (int) ($conversor->convert($valuePrice, $currencyToConvert) * 100);
        }

        $priceWithConversions = ScooterPrice::createPrice($valuePrice, $currency, $pricesConverted);
        $scooter->setPrice($priceWithConversions);
        $this->repository->save($scooter);
    }
}