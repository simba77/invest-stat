<?php

declare(strict_types=1);

namespace Modules\Markets\DataProviders;

use Illuminate\Support\Facades\Http;
use Modules\Accounts\Models\Asset;
use Modules\Markets\Securities;

class YahooFinance
{
    private string $apiUrl = 'https://alpha.financeapi.net/market/get-realtime-prices';

    public function __construct(
        private Securities $securities,
        private InvestCab $investCab
    ) {
    }

    public function import(): void
    {
        // Get all unique tickers
        $assets = Asset::query()
            ->select('ticker')
            ->where('stock_market', '=', 'SPB')
            ->groupBy('ticker')
            ->get()
            ->pluck('ticker')
            ->chunk(10);

        // Get data for the tickers
        foreach ($assets as $asset) {
            $data = Http::withHeaders(['x-api-key' => config('invest.yahooFinanceToken')])
                ->get($this->apiUrl, ['symbols' => $asset->implode(',')])
                ->json();

            $stocksData = $data['data'];
            foreach ($stocksData as $stock) {
                $attributes = $stock['attributes'];

                // If there is no data for the ticker, use investcab
                if (empty($attributes['name'])) {
                    $investCabData = $this->investCab->getDataByTicker($stock['id']);
                    $attributes['name'] = $investCabData['name'];
                    $attributes['last'] = $investCabData['price'];
                }

                $this->securities->createOrUpdate($stock['id'], 'SPB', [
                    'name'       => $attributes['name'],
                    'short_name' => $attributes['name'],
                    'lat_name'   => $attributes['name'],
                    'lot_size'   => 1,
                    'price'      => $attributes['last'],
                    'currency'   => 'USD',
                ]);
            }
        }
    }
}
