<?php

namespace App\Console\Commands;

use App\Services\RateService;
use Illuminate\Console\Command;
use RedisException;
use Throwable;

class RatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rates:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates rates via API';

    /**
     * Execute the console command.
     *
     * @throws RedisException
     * @throws Throwable
     */
    public function handle(RateService $rateService): void
    {
        try {
            $rates = $rateService->fetchRates();
        } catch (Throwable $e) {
            echo 'Failed to fetch rates: "', $e->getMessage(), '"', PHP_EOL;
            $this->error(sprintf('Failed to fetch rates: "%s"', $e->getMessage()));

            return;
        }
        $coll = $rateService->getRates(null, $rates)?->toJson();

        if ($coll !== null) {
            $rateService->saveRates($coll);
        }
    }
}
