<?php

declare(strict_types=1);

namespace Modules\Markets;

use Modules\Markets\Models\Security;

class Securities
{
    public function createOrUpdate(string $ticker, string $market, array $params): void
    {
        $fields = [
            'name'       => $params['name'] ?? null,
            'short_name' => $params['short_name'] ?? null,
            'lat_name'   => $params['lat_name'] ?? null,
            'lot_size'   => $params['lot_size'] ?? 1,
            'price'      => $params['price'] ?? 0,
            'currency'   => $this->getCurrencyByCode($params['currency']),
            'isin'       => $params['isin'] ?? '',
            'is_future'  => $params['is_future'] ?? null,
            'step_price' => $params['step_price'] ?? null,
            'expiration' => $params['expiration'] ?? null,
        ];

        foreach ($fields as $key => $field) {
            if (is_null($field)) {
                unset($fields[$key]);
            }
        }

        if (! isset($params['price'])) {
            unset($fields['price']);
        }

        Security::query()->updateOrCreate(['ticker' => $ticker, 'stock_market' => $market], $fields);
    }

    public function getCurrencyByCode(string $currency): string
    {
        $currencies = [
            'SUR' => 'RUB',
            'USD' => 'USD',
        ];
        return $currencies[$currency] ?? 'RUB';
    }
}
