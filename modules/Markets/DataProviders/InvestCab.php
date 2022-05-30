<?php

declare(strict_types=1);

namespace Modules\Markets\DataProviders;

use Illuminate\Support\Facades\Cache;

class InvestCab
{
    public function getDataByTicker(string $ticker): array
    {
        $tickerData = Cache::remember('tickerData' . $ticker, 86400, function () use ($ticker) {
            $data = file_get_contents('https://investcab.ru/api/symbol?symbol=' . $ticker);
            return json_decode(json_decode($data, true), true);
        });

        $chart = file_get_contents('https://investcab.ru/api/chistory?symbol=' . $ticker . '&resolution=30&from=' . (time() - 30) . '&to=' . time());
        $chartData = json_decode(json_decode($chart, true), true)['c'];

        return [
            'name'  => $tickerData['description'],
            'price' => last($chartData),
        ];
    }
}
