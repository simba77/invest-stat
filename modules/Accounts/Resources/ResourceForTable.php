<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
use Modules\Markets\Models\Security;

class ResourceForTable
{
    private float $total = 0;
    public static array $securities = [];

    /**
     * @var Account[]
     */
    private Collection | array $accounts;

    public function __construct(Collection | array $accounts)
    {
        $this->accounts = $accounts;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this->accounts as $account) {
            $assets = $this->getAssets($account);
            $collection = collect($assets)->where('isSubTotal', '=', true)->first();
            $currentAssetsPrice = 0;
            if ($collection) {
                $currentAssetsPrice = $collection['fullPrice'];
            }
            $currentValue = round($currentAssetsPrice + (float) $account->balance, 2);

            $data[] = [
                'id'           => $account->id,
                'name'         => $account->name,
                'balance'      => $account->balance,
                'deposits'     => $account->deposits_sum_sum ?? 0,
                'currency'     => $account->currency === 'RUB' ? '₽' : '$',
                'assets'       => $assets,
                'currentValue' => $currentValue,
                'fullProfit'   => round($currentValue - (float) $account->deposits_sum_sum, 2),
            ];
        }

        return ['data' => $data];
    }

    /**
     * Get assets for account
     */
    private function getAssets(Account $account): array
    {
        $items = [];
        foreach ($account->assets as $asset) {
            $stock = $this->getSecurity($asset->stock_market, $asset->ticker);
            $fillBuyPrice = $asset->quantity * $asset->buy_price;
            // Current full price
            $fullPrice = $asset->quantity * ($stock?->price ?? 0);
            $fullTargetPrice = $asset->quantity * ($asset?->target_price ?? 0);

            if ($asset->type === Asset::TYPE_SHORT) {
                $profit = $fillBuyPrice - $fullPrice;
            } else {
                $profit = $fullPrice - $fillBuyPrice;
            }

            $commission = $fullPrice * ($account->commission / 100);
            $profit = round($profit - $commission, 2);

            $items[] = [
                'id'              => $asset->id,
                'isShort'         => $asset->type === Asset::TYPE_SHORT,
                'updated'         => $stock?->updated_at?->timezone(config('app.timezone'))->format('d.m.Y H:i:s'),
                'ticker'          => $asset->ticker,
                'name'            => $stock?->short_name ?? '',
                'stockMarket'     => $asset->stock_market,
                'buyPrice'        => $asset->buy_price,
                'sellPrice'       => $asset->sell_price,
                'price'           => $stock?->price ?? 0,
                'targetPrice'     => $asset?->target_price ?? '',
                'quantity'        => $asset->quantity,
                'fullBuyPrice'    => $fillBuyPrice,
                'fullPrice'       => $fullPrice,
                'fullTargetPrice' => $fullTargetPrice > 0 ? $fullTargetPrice : '',
                'profit'          => $profit,
                'commission'      => round($commission, 2),
                'profitPercent'   => round($profit / $fillBuyPrice * 100, 2),
                'accountPercent'  => round($fullPrice / ($account->current_sum_of_assets + $account->balance) * 100, 2),
                'currency'        => getCurrencyName($stock?->currency ?? 'USD'),
                'items'           => [],
                'showItems'       => false,
                'blocked'         => $asset->blocked,
            ];
            $this->total += $asset->sum;
        }

        /** @var \Illuminate\Support\Collection[] $collection */
        $collection = collect($items)->groupBy('ticker');
        $items = [];
        foreach ($collection as $item) {
            // Group of assets
            if ($item->count() > 1) {
                $subItems = $item->toArray();
                $avgItem = $this->getAvgValues($subItems);
                $profit = round($avgItem['fullPrice'] - $avgItem['fullBuyPrice'] - $avgItem['commission']);

                $items[] = [
                    'id'              => $subItems[0]['id'],
                    'ticker'          => $subItems[0]['ticker'],
                    'updated'         => $subItems[0]['updated'],
                    'name'            => $subItems[0]['name'],
                    'stockMarket'     => $subItems[0]['stockMarket'],
                    'buyPrice'        => $avgItem['buyPrice'],
                    'sellPrice'       => $subItems[0]['sellPrice'],
                    'price'           => $subItems[0]['price'],
                    'targetPrice'     => $avgItem['targetPrice'],
                    'quantity'        => $avgItem['quantity'],
                    'fullBuyPrice'    => $avgItem['fullBuyPrice'],
                    'fullPrice'       => $avgItem['fullPrice'],
                    'fullTargetPrice' => $avgItem['fullTargetPrice'],
                    'commission'      => round($avgItem['commission'], 2),
                    'profit'          => $profit,
                    'profitPercent'   => round($profit / $avgItem['fullBuyPrice'] * 100, 2),
                    'accountPercent'  => round($avgItem['fullPrice'] / ($account->current_sum_of_assets + $account->balance) * 100, 2),
                    'currency'        => $subItems[0]['currency'],
                    'items'           => $subItems,
                    'showItems'       => false,
                ];
            } else {
                $items[] = $item->toArray()[0];
            }
        }

        // Total row
        if (! empty($items)) {
            $fullProfit = array_sum(array_column($items, 'profit'));
            $fullBuyPrice = array_sum(array_column($items, 'fullBuyPrice'));
            $fullProfitPercent = round($fullProfit / $fullBuyPrice * 100, 2);
            $items[] = [
                'name'          => 'Subtotal:',
                'isSubTotal'    => true,
                'fullBuyPrice'  => $fullBuyPrice,
                'fullPrice'     => array_sum(array_column($items, 'fullPrice')),
                'profit'        => $fullProfit,
                'profitPercent' => $fullProfitPercent,
                'currency'      => getCurrencyName($stock->currency ?? ''),
            ];
        }

        return $items;
    }

    private function getSecurity(string $market, string $ticker)
    {
        $security = Arr::get(self::$securities, $market . '.' . $ticker);
        if ($security) {
            return $security;
        }

        $security = Security::query()->where('stock_market', $market)->where('ticker', $ticker)->first();
        self::$securities[$market][$ticker] = $security;
        return $security;
    }

    /**
     * Average data for group of assets
     */
    private function getAvgValues(array $subItems): array
    {
        $quantity = array_sum(array_column($subItems, 'quantity'));
        $fullBuyPrice = array_sum(array_column($subItems, 'fullBuyPrice'));
        $fullPrice = array_sum(array_column($subItems, 'fullPrice'));
        $commission = array_sum(array_column($subItems, 'commission'));
        $fullTargetPrice = array_sum(array_column($subItems, 'fullTargetPrice'));

        return [
            'buyPrice'        => round($fullBuyPrice / $quantity, 2),
            'quantity'        => $quantity,
            'fullBuyPrice'    => $fullBuyPrice,
            'fullPrice'       => $fullPrice,
            'targetPrice'     => round($fullTargetPrice / $quantity, 2),
            'fullTargetPrice' => $fullTargetPrice,
            'commission'      => $commission,
        ];
    }
}
