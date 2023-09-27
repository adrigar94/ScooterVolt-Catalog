<?php

declare(strict_types=1);

namespace ScooterVolt\CatalogService\Shared\Application;

use Exception;

class CurrencyConversor
{
    private const endpoint = 'https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/';

    public static function convert(float $value, string $from, string $to): float
    {

        $from = strtolower($from);
        $to = strtolower($to);

        $rates = self::getCurrencyRates($from);

        if (!isset($rates[$from][$to])) {
            throw new \RuntimeException('Invalid currency');
        }

        $rate = $rates[$from][$to];

        return $value * $rate;
    }


    private static function getCurrencyRates(string $currency): array
    {
        $url = self::endpoint . $currency . '.json';
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