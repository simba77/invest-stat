<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounts\Models\Account;
use Modules\Accounts\Models\Asset;

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
                'expenses' => $this->getAssets($account->assets),
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
     * @param Collection|Asset[] $assets
     * @return array
     */
    private function getAssets(Collection | array $assets): array
    {
        $items = [];
        foreach ($assets as $asset) {
            $items[] = [
                'id'          => $asset->id,
                'ticker'      => $asset->ticker,
                'stockMarket' => $asset->stock_market,
                'buyPrice'    => $asset->buy_price,
                'sellPrice'   => $asset->sell_price,
                'currency'    => getCurrencyName($asset->currency),
            ];
            $this->total += $asset->sum;
        }

        $items[] = [
            'name'       => 'Subtotal:',
            'isSubTotal' => true,
            'sum'        => array_sum(array_column($items, 'sum')),
            'currency'   => '₽',
        ];

        return $items;
    }
}
