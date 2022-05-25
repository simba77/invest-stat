<?php

declare(strict_types=1);

/**
 * Returns the name of the currency by the currency code
 */
function getCurrencyName(string $currencyCode): string
{
    $names = [
        'USD' => '$',
        'RUB' => '₽',
    ];
    return array_key_exists($currencyCode, $names) ? $names[$currencyCode] : '';
}
