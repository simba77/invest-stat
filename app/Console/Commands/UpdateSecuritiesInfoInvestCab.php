<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Accounts\Services\AccountService;
use Modules\Markets\DataProviders\InvestCab;
use Modules\Markets\DataProviders\Moex;
use Modules\Markets\DataProviders\YahooFinance;

class UpdateSecuritiesInfoInvestCab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'securities:update-data-investcab';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update securities data';

    public function handle(InvestCab $investCab): int
    {
        $investCab->updateData($this);

        return 0;
    }
}
