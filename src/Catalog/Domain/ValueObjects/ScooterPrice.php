<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Domain\Currency\CurrencyValueObject;
use Adrigar94\ValueObjectCraft\Domain\Price\PriceValueObject;

class ScooterPrice extends PriceValueObject
{
    private array $priceConversions = [];

    public static function createPrice(float $price, CurrencyValueObject $currency, array $priceConversions = []): self
    {
        $price = parent::createPrice($price, $currency);
        $price->priceConversions = $priceConversions;
        return $price;
    }

    public static function fromNative($native)
    {
        $price = parent::fromNative($native);

        $price->priceConversions = $native['price_conversions'] ?? [];

        return $price;
    }

    public function toNative(): array
    {
        $native = parent::toNative();
        $native['price_conversions'] = $this->priceConversions;
        return $native;
    }

    public function getPriceConversions(): array
    {
        return $this->priceConversions;
    }
}