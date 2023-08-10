<?php

declare(strict_types=1);

namespace Modules\Analytics\Controllers;

use Modules\Accounts\Models\Asset;
use Modules\Accounts\Services\AccountService;
use Modules\Accounts\Services\GroupAssetsCalculator;
use Modules\Markets\DataProviders\Moex;

class AnalyticsController
{
    public function index(AccountService $accountService, Moex $moex)
    {
        $assets = Asset::query()->active()->get();

        $assetsGroups = $assets->groupBy('ticker');

        $list = [];
        foreach ($assetsGroups as $assetsGroup) {
            $allAssetsSum = $accountService->getAllAssetsSum();
            $groupCalculator = new GroupAssetsCalculator($assetsGroup, $moex, $allAssetsSum, 0);
            $isGroup = $assetsGroup->count() > 1;

            /** @var Asset $firstAsset */
            $firstAsset = $assetsGroup->first();

            $list[] = [
                'group'       => $isGroup,
                'id'          => $firstAsset->id,
                'updatedAt'   => $firstAsset->security?->updated_at?->format('d.m.Y H:i:s'),
                'accountId'   => $firstAsset->account_id,
                'ticker'      => $firstAsset->ticker,
                'name'        => $firstAsset->security?->short_name,
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
                'items'    => [],

                'showItems' => false,
            ];
        }

        return $list;
    }
}
