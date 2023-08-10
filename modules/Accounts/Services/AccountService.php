<?php

declare(strict_types=1);

namespace Modules\Accounts\Services;

use Modules\Accounts\Models\Account;
use Modules\Markets\DataProviders\Moex;

class AccountService
{
    public function __construct(
        private Moex $moex
    ) {
    }

    public function updateAll(): void
    {
        $accounts = Account::query()->activeAssets()->get();
        foreach ($accounts as $account) {
            $this->updateSummary($account);
        }
    }

    public function updateSummary(Account $account): void
    {
        $startSum = 0;
        $currentSum = 0;
        foreach ($account->assets as $asset) {
            if ($asset->security?->is_future) {
                $startSum += 0;
                $currentSum += $asset->profit;
            } else {
                $startSum += $asset->full_buy_price;
                $currentSum += $asset->full_current_base_price;
            }
        }

        $account->start_sum_of_assets = $startSum;
        $account->current_sum_of_assets = $currentSum;
        $account->save();
    }

    /**
     * Метод возвращает суммарную стоимость всех активов на всех счетах включая баланс рубли/доллары.
     * Долларовые активы переводятся в базовую валюту по текущему курсу
     */
    public function getAllAssetsSum(): float
    {
        $allAssetsSum = 0;
        $accounts = Account::forCurrentUser()->activeAssets()->orderBy('sort')->get();
        foreach ($accounts as $account) {
            $currentSumOfAssets = $account->current_sum_of_assets + $account->balance + ($account->usd_balance * $this->moex->getRate());
            $allAssetsSum += $currentSumOfAssets;
        }

        return $allAssetsSum;
    }
}
