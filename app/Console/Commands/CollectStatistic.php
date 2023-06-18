<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Statistic\StatisticCollector;

class CollectStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:collect-for-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collect statistic for accounts';

    public function handle(StatisticCollector $collector): int
    {
        $collector->collectStatisticForAccounts();
        return 0;
    }
}
