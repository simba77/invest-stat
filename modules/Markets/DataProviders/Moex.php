<?php

declare(strict_types=1);

namespace Modules\Markets\DataProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\Markets\Securities;

class Moex
{
    private string $moexStocksUrl = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQBR/securities.xml';
    private string $moexEtfsUrl = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQTF/securities.xml';
    private string $moexShares = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQIF/securities.xml';
    private string $rates = 'https://iss.moex.com/iss/statistics/engines/futures/markets/indicativerates/securities.xml';

    public function __construct(private Securities $securities)
    {
    }

    public function import(): void
    {
        $xmlDataString = Http::get($this->moexStocksUrl)->body();
        $this->processData($xmlDataString);
    }

    public function importEtf(): void
    {
        $xmlDataString = Http::get($this->moexEtfsUrl)->body();
        $this->processData($xmlDataString);
    }

    public function importShares(): void
    {
        $xmlDataString = Http::get($this->moexShares)->body();
        $this->processData($xmlDataString);
    }

    private function processData(string $xmlDataString): void
    {
        $xmlObject = simplexml_load_string($xmlDataString);
        $data = json_decode(json_encode($xmlObject), true) ?? [];

        $stocks = $data['data'][0]['rows']['row'];

        // Get market data to collection
        $marketData = collect(array_column($data['data'][1]['rows']['row'], '@attributes'));

        foreach ($stocks as $stock) {
            $stock = $stock['@attributes'];

            $price = $marketData->where('SECID', '=', $stock['SECID'])->first()['LCURRENTPRICE'];

            if (! empty($price)) {
                $this->securities->createOrUpdate($stock['SECID'], 'MOEX', [
                    'name'       => $stock['SECNAME'],
                    'short_name' => $stock['SHORTNAME'],
                    'lat_name'   => $stock['LATNAME'],
                    'isin'       => $stock['ISIN'],
                    'lot_size'   => $stock['LOTSIZE'],
                    'price'      => ! empty($price) ? $price : 0,
                    'currency'   => $stock['CURRENCYID'],
                ]);
            }
        }
    }

    public function getRate($currency = 'USD/RUB'): float
    {
        return Cache::remember('currencyRate' . $currency, 30, function () use ($currency) {
            $xmlDataString = Http::get($this->rates)->body();
            $xmlObject = simplexml_load_string($xmlDataString);
            $data = json_decode(json_encode($xmlObject), true) ?? [];
            $collection = collect($data['data'][0]['rows']['row'] ?? []);
            $rate = $collection->where('@attributes.secid', '=', $currency)->first() ?? [];
            return (float) Arr::get($rate, '@attributes.rate', 0);
        });
    }
}
