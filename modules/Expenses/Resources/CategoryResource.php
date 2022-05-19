<?php

declare(strict_types=1);

namespace Modules\Expenses\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Expenses\Models\ExpensesCategory;

/**
 * @mixin ExpensesCategory
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'expenses' => $this->getExpenses(),
        ];
    }

    private function getExpenses(): array
    {
        return [
            [
                'name' => 'Test name',
                'sum'  => '4333.23',
            ],
            [
                'name' => 'Test name 2',
                'sum'  => '4333.23',
            ],
            [
                'name' => 'Test name 3',
                'sum'  => '4333.23',
            ],
            [
                'isSubTotal' => true,
                'name'       => 'Subtotal:',
                'sum'        => '4333.23',
            ],
            [
                'isTotal' => true,
                'name'    => 'Total:',
                'sum'     => '4333.23',
            ],
        ];
    }
}
