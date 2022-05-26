<?php

declare(strict_types=1);

namespace Modules\Markets\Moex;

use Modules\Markets\Securities;

class Stocks
{
    private string $moexStocksUrl = 'https://iss.moex.com/iss/engines/stock/markets/shares/boards/TQBR/securities.xml';

    public function __construct(private Securities $securities)
    {
    }

    public function import(): void
    {
        $xmlDataString = file_get_contents($this->moexStocksUrl);
        $xmlObject = simplexml_load_string($xmlDataString);
        $data = json_decode(json_encode($xmlObject), true) ?? [];

        $stocks = $data['data'][0]['rows']['row'];

        // Get market data to collection
        $marketData = collect(array_column($data['data'][1]['rows']['row'], '@attributes'));

        foreach ($stocks as $stock) {
            $stock = $stock['@attributes'];

            $price = $marketData->where('SECID', '=', $stock['SECID'])->first()['LCURRENTPRICE'];

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
