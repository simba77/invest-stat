<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Markets\DataProviders\Moex;
use Modules\Markets\DataProviders\YahooFinance;

class UpdateSecuritiesInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'securities:update-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update securities data';

    public function handle(Moex $moex, YahooFinance $yahooFinance): int
    {
        $moex->import();
        $moex->importEtf();
        $yahooFinance->import();

        return 0;
    }
}
