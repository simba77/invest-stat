<?php

declare(strict_types=1);

namespace Modules\Accounts\Resources;

use Illuminate\Database\Eloquent\Collection;
use Modules\Expenses\Models\Expense;
use Modules\Expenses\Models\ExpensesCategory;

class ResourceForTable
{
    private float $total = 0;

    /**
     * @var ExpensesCategory[]
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
