<?php

declare(strict_types=1);

namespace Modules\Savings\Services;

use Illuminate\Support\Collection;
use Modules\Savings\Models\Saving;

class SavingsService
{
    /**
     * Список счетов с суммарным балансом на каждом из них
     */
    public function getGroupedByAccount(): Collection
    {
        return Saving::query()
            ->groupBy('saving_account_id')
            ->selectRaw('sum(sum) as sum, saving_account_id')
            ->with('account')
            ->get()
            ->map(fn($item) => [
                'id'   => $item->saving_account_id,
                'name' => $item->account->name,
                'sum'  => $item->sum,
            ]);
    }
}
