<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Tests\Shared\Application;

use PHPUnit\Framework\TestCase;
use ScooterVolt\CatalogService\Shared\Application\CurrencyConversor;

class CurrencyConversorTest extends TestCase
{
    public function testCurrencyConversorApiWorks(): void
    {
        $conversor = new CurrencyConversor('EUR');
        $eur_value = 10;
        $usd_value = $conversor->convert($eur_value, 'USD');

        $this->assertIsFloat($usd_value);
    }
}
