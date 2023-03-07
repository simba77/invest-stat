<?php

declare(strict_types=1);

namespace Modules\Accounts\Services;

use Illuminate\Support\Collection;
use Modules\Accounts\Models\Asset;

class GroupAssetsCalculator
{
    private Asset $firstAsset;

    /**
     * @param Collection | Asset[] $collection
     */
    public function __construct(
        protected array | Collection $collection
    ) {
        $this->firstAsset = $this->collection->first();
    }

    /**
     * Средняя цена покупки
     */
    public function getAvgBuyPrice(): float
    {
        return round($this->collection->sum('full_buy_price') / $this->collection->sum('quantity'), 4);
    }

    /**
     * Полная стоимость ценных бумаг на момент покупки
     */
    public function getFullBuyPrice(): float
    {
        return round($this->collection->sum('full_buy_price'), 4);
    }

    /**
     * Количество
     */
    public function getQuantity(): int
    {
        return (int) $this->collection->sum('quantity');
    }

    /**
     * Средняя целевая цена
     */
    public function getAvgTargetPrice(): float
    {
        return round($this->collection->sum('full_target_price') / $this->collection->sum('quantity'), 4);
    }

    /**
     * Полная целевая стоимость всех ценных бумаг
     */
    public function getFullTargetPrice(): float
    {
        return round($this->collection->sum('full_target_price'), 4);
    }

    /**
     * Текущая прибыль
     */
    public function getProfit(): float
    {
        return round($this->collection->sum('profit'), 4);
    }

    /**
     * Полная текущая стоимость всех ценных бумаг
     */
    public function getFullCurrentPrice(): float
    {
        return round($this->collection->sum('full_current_price'), 4);
    }

    /**
     * Суммарная комиссия по всем ценным бумагам
     */
    public function getCommission(): float
    {
        return round($this->collection->sum('commission'), 2);
    }

    /**
     * Целевой усредненный доход
     */
    public function getTargetProfit(): float
    {
        if ($this->getAvgTargetPrice() == 0) {
            return 0;
        }
        if ($this->firstAsset->type === Asset::TYPE_SHORT) {
            return round($this->getAvgBuyPrice() - $this->getAvgTargetPrice(), 2);
        }
        return round($this->getAvgTargetPrice() - $this->getAvgBuyPrice(), 2);
    }

    /**
     * Полный целевой доход от указанной группы активов
     */
    public function getFullTargetProfit(): float
    {
        if ($this->getFullTargetPrice() == 0) {
            return 0;
        }

        if ($this->firstAsset->type === Asset::TYPE_SHORT) {
            return round($this->getFullBuyPrice() - $this->getFullTargetPrice(), 2);
        }
        return round($this->getFullTargetPrice() - $this->getFullBuyPrice(), 2);
    }

    /**
     * Полный целевой доход в процентах
     */
    public function getFullTargetProfitPercent(): float
    {
        if ($this->getFullTargetPrice() == 0) {
            return 0;
        }

        if ($this->firstAsset->type === Asset::TYPE_SHORT) {
            return round(($this->getFullBuyPrice() - $this->getFullTargetPrice()) / $this->getFullBuyPrice() * 100, 2);
        }
        return round(($this->getFullTargetPrice() - $this->getFullBuyPrice()) / $this->getFullBuyPrice() * 100, 2);
    }

    /**
     * Текущий процент дохода
     */
    public function getProfitPercent(): float
    {
        return round($this->getProfit() / $this->getFullBuyPrice() * 100, 2);
    }

    /**
     * Текущая цена за лот
     */
    public function getCurrentPrice(): float
    {
        return $this->firstAsset->current_price;
    }

    /**
     * Доля в портфеле
     */
    public function getGroupPercent(): float
    {
        return round($this->getFullCurrentPrice() / ($this->firstAsset->account->current_sum_of_assets + $this->firstAsset->account->balance) * 100, 2);
    }
}
