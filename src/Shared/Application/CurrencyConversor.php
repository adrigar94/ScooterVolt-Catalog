<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Application;

use Exception;

class CurrencyConversor
{
    private const endpoint = 'https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/';

    private string $fromCurrency;

    private array $rates;

    public function __construct(string $fromCurrency)
    {
        $this->fromCurrency = strtolower($fromCurrency);
        $this->rates = self::getCurrencyRates();
    }

    public function convert(float $value, string $toCurrency): float
    {

        $toCurrency = strtolower($toCurrency);

        if (!isset($this->rates[$this->fromCurrency][$toCurrency])) {
            throw new \RuntimeException('Invalid currency');
        }

        $rate = $this->rates[$this->fromCurrency][$toCurrency];

        return $value * $rate;
    }


    private function getCurrencyRates(): array
    {
        $url = self::endpoint . $this->fromCurrency . '.json';
        try {
            $jsonData = file_get_contents($url);
            $rates = json_decode($jsonData ?: "", true);

            if (is_null($rates)) {
                throw new Exception('"' . print_r($jsonData, true) . '" can\'t be decoded');
            }
        } catch (Exception $e) {
            throw new Exception("Can't get currency rates\n" . $e->getMessage());
        }
        return $rates;
    }
}