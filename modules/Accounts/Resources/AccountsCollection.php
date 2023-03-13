<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
use Modules\Accounts\Services\GroupAssetsCalculator;
use Modules\Markets\DataProviders\Moex;

/** @see \Modules\Accounts\Models\Account */
class AccountsCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): Collection
    {
        $moex = app(Moex::class);
        return $this->collection->map(function (Account $account) use ($moex) {
            $currentValue = round($account->current_sum_of_assets + $account->balance + ($account->usd_balance * $moex->getRate()), 2);
            return [
                'id'           => $account->id,
                'name'         => $account->name,
                'balance'      => $account->balance,
                'usdBalance'   => $account->usd_balance,
                'deposits'     => $account->deposits_sum_sum ?? 0,
                'currentValue' => $currentValue,
                'fullProfit'   => round($currentValue - (float) $account->deposits_sum_sum, 2),
                'blockGroups'  => $this->groupBlockAssets($account->assets),
            ];
        });
    }

    /**
     * Group assets by block-status, currency and ticker
     */
    private function groupBlockAssets(Collection $assets)
    {
        // Group by ability status
        return $assets->groupBy('blocked')
            ->map(function (Collection $groupByAbility, $key) {
                return [
                    'name'    => $key ? 'Blocked' : 'Available',
                    'blocked' => (bool) $key,
                    'items'   => $this->getGroupedAssetsByCurrency($groupByAbility),
                ];
            })
            ->sortBy('name')
            ->values();
    }

    /**
     * Group by currency
     */
    private function getGroupedAssetsByCurrency(Collection $groupByAbility)
    {
        return $groupByAbility->groupBy('currency')
            ->map(function (Collection $groupByCurrency, $key) {
                $names = [
                    'RUB' => 'Russian Rouble',
                    'USD' => 'Dollar',
                ];

                return [
                    'name'  => Arr::get($names, $key, 'Currency'),
                    'items' => $this->getGroupedAssetsByTicker($groupByCurrency),
                ];
            })->sortByDesc('name');
    }

    /**
     * Group by ticker
     */
    private function getGroupedAssetsByTicker(Collection $groupByCurrency)
    {
        $groups = $groupByCurrency->groupBy('ticker')
            ->map(fn($assets) => $this->prepareAssetsGroupRow($assets))
            ->values();

        $currency = $groups->first()['currency'];

        // Итог по группе
        $fullBuyPrice = $groups->sum('fullBuyPrice');
        $fullCurrentPrice = $groups->sum('fullCurrentPrice');
        $profit = $groups->sum('profit');
        if ($currency === 'USD') {
            $rate = app(Moex::class)->getRate();
            $fullBuyPriceConverted = $fullBuyPrice * $rate;
            $fullCurrentPriceConverted = $fullCurrentPrice * $rate;
            $profitConverted = $profit * $rate;
        }

        $groups->push(
            [
                'currency'                  => $currency,
                'isBaseCurrency'            => $currency === 'RUB',
                'isSubTotal'                => true,
                'fullBuyPrice'              => $fullBuyPrice,
                'fullBuyPriceConverted'     => $fullBuyPriceConverted ?? $fullBuyPrice,
                'fullCurrentPrice'          => $fullCurrentPrice,
                'fullCurrentPriceConverted' => $fullCurrentPriceConverted ?? $fullCurrentPrice,
                'profit'                    => $profit,
                'profitConverted'           => $profitConverted ?? $profit,
                'profitPercent'             => round(($fullCurrentPrice - $fullBuyPrice - $groups->sum('fullCommission')) / $fullBuyPrice * 100, 2),
            ]
        );

        return $groups;
    }

    /**
     * Текущая стоимость активов в пересчете на базовую валюту
     */
    private function getCurrentValue(Collection $assets)
    {
        return $assets->sum('full_current_base_price');
    }

    private function prepareAssetsGroupRow(Collection $assets): array
    {
        $isGroup = $assets->count() > 1;

        /** @var Asset $firstAsset */
        $firstAsset = $assets->first();

        $groupCalculator = new GroupAssetsCalculator($assets, app(Moex::class));

        return [
            'group'       => $isGroup,
            'id'          => $isGroup ? null : $firstAsset->id,
            'accountId'   => $firstAsset->account_id,
            'ticker'      => $firstAsset->ticker,
            'name'        => $firstAsset->security->short_name,
            'stockMarket' => $firstAsset->stock_market,
            'blocked'     => $firstAsset->blocked,
            'quantity'    => $groupCalculator->getQuantity(),

            'avgBuyPrice'  => $groupCalculator->getAvgBuyPrice(),
            'fullBuyPrice' => $groupCalculator->getFullBuyPrice(),

            'avgTargetPrice'  => $groupCalculator->getAvgTargetPrice(),
            'fullTargetPrice' => $groupCalculator->getFullTargetPrice(),

            'currentPrice'     => $groupCalculator->getCurrentPrice(),
            'fullCurrentPrice' => $groupCalculator->getFullCurrentPrice(),

            'profit'         => $groupCalculator->getProfit(),
            'profitPercent'  => $groupCalculator->getProfitPercent(),
            'fullCommission' => $groupCalculator->getCommission(),

            'targetProfit'            => $groupCalculator->getTargetProfit(),
            'fullTargetProfit'        => $groupCalculator->getFullTargetProfit(),
            'fullTargetProfitPercent' => $groupCalculator->getFullTargetProfitPercent(),

            'groupPercent' => $groupCalculator->getGroupPercent(),

            'currency' => $firstAsset->currency,
            'type'     => $firstAsset->type,
            'isShort'  => $firstAsset->type === Asset::TYPE_SHORT,

            // All items
            'items'    => AssetResource::collection($assets),

            'showItems' => false,
        ];
    }
}
