<?php

/**
 * RateRepository
 * php version 8.3
 *
 * @category Repositories
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\Repositories;

use App\DataFilters\FeeRatesFilter;
use App\DataFilters\ReduceByTickerFilter;
use App\DataFilters\SortByRateFilter;
use App\DataFilters\TransformJsonFilter;
use App\Exceptions\ApplicationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use RedisException;
use Throwable;

/**
 * RateRepository
 * php version 8.3
 *
 * @category Repositories
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
class RateRepository
{
    public const int PRECISION_CRYPTO = 10;
    public const int PRECISION_FIAT = 2;

    private const string URL = 'https://blockchain.info/ticker';

    private const string KEY = 'rates';

    public function __construct(
        private readonly TransformJsonFilter $transformJsonFilter,
        private readonly FeeRatesFilter $ratesFilter,
        private readonly SortByRateFilter $sortByRateFilter,
        private readonly ReduceByTickerFilter $reduceByTickerFilter,
    ) {
    }

    public function fetchRates(): string
    {
        return Http::get(self::URL)->body();
    }

    /**
     * @throws ApplicationException
     */
    public function transformJson(string $string): ?Collection
    {
        return $this->transformJsonFilter->apply($string);
    }

    public function filterRates(Collection $collection): Collection
    {
        return $this->ratesFilter->apply($collection);
    }

    public function filterSortRates(Collection $collection): Collection
    {
        return $this->sortByRateFilter->apply($collection);
    }

    public function filterReduceByTickers(array $tickers, Collection $collection): Collection
    {
        $this->reduceByTickerFilter->setTickers($tickers);

        return $this->reduceByTickerFilter->apply($collection);
    }

    /**
     * @throws Throwable
     * @throws RedisException
     */
    public function saveRates(string $string): void
    {
        Redis::connection()->client()->set(self::KEY, $string);
    }

    /**
     * @throws RedisException
     */
    public function getRates(): ?string
    {
        return Redis::connection()->client()->get(self::KEY);
    }

    public function convert(float $s, float $d, float $amount, int $precision = self::PRECISION_CRYPTO): string
    {
        return bcmul((string) $amount, bcdiv((string) $d, (string) $s, $precision), self::PRECISION_FIAT);
    }

    public function convertFromBTC(string $amount, float $rate): string
    {
        return bcmul($amount, (string) $rate, self::PRECISION_FIAT);
    }

    public function convertToBTC(string $amount, float $rate, int $precision = self::PRECISION_CRYPTO): string
    {
        return bcdiv($amount, (string) $rate, $precision);
    }

    public function calculateRate(string $before, string $after, int $precision = self::PRECISION_CRYPTO): string
    {
        return bcdiv($after, $before, $precision);
    }
}
