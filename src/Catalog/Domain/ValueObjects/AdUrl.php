<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Catalog\Domain\ValueObjects;

use Adrigar94\ValueObjectCraft\Primitive\String\StringValueObject;

class AdUrl extends StringValueObject
{
    protected static function getMinLength(): int
    {
        return 3;
    }

    protected static function getMaxLength(): int
    {
        return 70;
    }

    public static function generateRandomUrlForBlankAd(): self
    {
        return new static(uniqid());
    }

    public static function generateUrlFromTitle(string $title): self
    {
        $url = str_replace(' ', '-', $title);

        $url = preg_replace('/[^A-Za-z0-9\-]/', '', $url);

        $url = strtolower($url);
        $url = trim($url, '-');
        $url = urlencode($url);
        $url .= '-';
        return new static(uniqid($url));
    }
}
