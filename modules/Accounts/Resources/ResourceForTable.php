<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;
use Modules\Markets\Models\Security;

class ResourceForTable
{
    private float $total = 0;

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
        $expenses = [];
        foreach ($this->accounts as $account) {
            $expenses[] = [
                'id'       => $account->id,
                'name'     => $account->name,
                'balance'  => $account->balance,
                'currency' => $account->currency === 'RUB' ? '₽' : '$',
                'expenses' => $this->getAssets($account),
            ];
        }

        $expenses[] = [
            'name'     => 'Total:',
            'isTotal'  => true,
            'sum'      => $this->total,
            'currency' => '₽',
        ];

        return ['data' => $expenses];
    }

    /**
     * @param Account $account
     * @return array
     */
    private function getAssets(Account $account): array
    {
        $items = [];
        foreach ($account->assets as $asset) {
            $stock = Security::query()->where('stock_market', $asset->stock_market)->where('ticker', $asset->ticker)->first();
            $fillBuyPrice = $asset->quantity * $asset->buy_price;
            // Current full price
            $fullPrice = $asset->quantity * ($stock?->price ?? 0);
            $profit = $fullPrice - $fillBuyPrice;

            $items[] = [
                'id'             => $asset->id,
                'ticker'         => $asset->ticker,
                'name'           => $stock?->short_name ?? '',
                'stockMarket'    => $asset->stock_market,
                'buyPrice'       => $asset->buy_price,
                'sellPrice'      => $asset->sell_price,
                'price'          => $stock?->price ?? 0,
                'quantity'       => $asset->quantity,
                'fullBuyPrice'   => $fillBuyPrice,
                'fullPrice'      => $fullPrice,
                'profit'         => $profit,
                'profitPercent'  => round($profit / $fillBuyPrice * 100, 2),
                'accountPercent' => round($fullPrice / $account->current_sum_of_assets * 100, 2),
                'currency'       => getCurrencyName($stock?->currency ?? 'USD'),
            ];
            $this->total += $asset->sum;
        }

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
}
