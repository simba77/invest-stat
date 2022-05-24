<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounts\Models\Account;
use Modules\Expenses\Models\Expense;

class ResourceForTable
{
    private float $total = 0;

    /**
     * @var Account[]
     */
    private Collection | array $categories;

    public function __construct(Collection | array $categories)
    {
        $this->categories = $categories;
    }

    public function toArray(): array
    {
        $expenses = [];
        foreach ($this->categories as $category) {
            $expenses[] = [
                'id'       => $category->id,
                'name'     => $category->name,
                'balance'  => $category->balance,
                'currency' => $category->currency === 'RUB' ? '₽' : '$',
                'expenses' => $this->getExpenses($category->expenses),
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
     * @param Collection|Expense[] $expenses
     * @return array
     */
    private function getExpenses(Collection | array $expenses): array
    {
        $items = [];
        foreach ($expenses as $expense) {
            $items[] = [
                'id'       => $expense->id,
                'name'     => $expense->name,
                'sum'      => $expense->sum,
                'currency' => '₽',
            ];
            $this->total += $expense->sum;
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
