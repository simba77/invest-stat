<?php

declare(strict_types=1);

namespace Modules\Markets\DataProviders;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\Accounts\Models\Asset;
use Modules\Accounts\Services\AccountService;
use Modules\Markets\Securities;

class InvestCab
{
    public function __construct(
        private Securities $securities,
        private AccountService $accountService,
    ) {
    }

    /**
     * @throws \JsonException
     */
    public function getDataByTicker(string $ticker, bool $getName = false): array
    {
        if ($getName) {
            $tickerData = Cache::remember('tickerData' . $ticker, 86400, function () use ($ticker) {
                $data = file_get_contents('https://investcab.ru/api/symbol?symbol=' . $ticker);
                return json_decode(json_decode($data, true), true);
            });
        }

        $chart = file_get_contents('https://investcab.ru/api/chistory?symbol=' . $ticker . '&resolution=30&from=' . (time() - 86400 * 3) . '&to=' . time());

        $chartData = json_decode(
            json_decode($chart, true, 512, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        )['c'];

        $price = last($chartData);

        if (empty($price)) {
            throw new \RuntimeException('Unable to get price');
        }

        return [
            'name'  => ($getName && $tickerData) ? $tickerData['description'] : null,
            'price' => $price,
        ];
    }

    public function updateData(?Command $console = null): void
    {
        $assets = Asset::query()
            ->select('ticker')
            ->where('stock_market', '=', 'SPB')
            ->whereNull('status')
            ->groupBy('ticker')
            ->get();

        foreach ($assets as $item) {
            try {
                $console?->info($item->ticker);

                $data = $this->getDataByTicker($item->ticker, true);
                $console?->info(json_encode($data));
                $this->securities->createOrUpdate($item->ticker, 'SPB', [
                    'lot_size'   => 1,
                    'short_name' => $data['name'],
                    'name'       => $data['name'],
                    'lat_name'   => $data['name'],
                    'price'      => $data['price'],
                    'currency'   => 'USD',
                ]);
            } catch (\Throwable $exception) {
                $console?->error($exception->getMessage());
            }

            $this->accountService->updateAll();
            sleep(rand(1, 5));
        }
    }
}
