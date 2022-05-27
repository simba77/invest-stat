<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Markets\DataProviders\Moex;

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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $stocksMoex = app(Moex::class);
        $stocksMoex->import();

        return 0;
    }
}
