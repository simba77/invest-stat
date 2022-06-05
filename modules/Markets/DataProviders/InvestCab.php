<?php

declare(strict_types=1);

namespace Modules\Markets\DataProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InvestCab
{
    public function getDataByTicker(string $ticker): array
    {
        $tickerData = Cache::remember('tickerData' . $ticker, 86400, function () use ($ticker) {
            $data = Http::get('https://investcab.ru/api/symbol', ['symbol' => $ticker])->body();
            return json_decode(json_decode($data, true), true);
        });

        $chart = Http::get('https://investcab.ru/api/chistory', [
            'symbol'     => $ticker,
            'resolution' => 30,
            'from'       => (time() - 86400 * 3),
            'to'         => time(),
        ])->body();
        $chartData = json_decode(json_decode($chart, true), true)['c'];

        return [
            'name'  => $tickerData['description'],
            'price' => last($chartData),
        ];
    }
}
