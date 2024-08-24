<?php

/**
 * RateService
 * php version 8.3
 *
 * @category Services
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\Services;

use App\DTO\ConvertDTO;
use App\Exceptions\ApplicationException;
use App\Repositories\RateRepository;
use Illuminate\Support\Collection;
use RedisException;
use Throwable;

/**
 * RateService
 * php version 8.3
 *
 * @category Services
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
readonly class RateService
{
    private const string BTC = 'BTC';

    private const float MIN_AMOUNT = 0.01;

    public function __construct(
        private RateRepository $rateRepository,
    ) {
    }

    /**
     * @param  ?array $reducedTickers
     * @param  ?string $rawRates
     *
     * @return Collection|null
     */
    public function getRates(?array $reducedTickers = null, ?string $rawRates = null): ?Collection
    {
        $rates = null;

        try {
            $rates = $rawRates ?? $this->rateRepository->getRates();
        } catch (Throwable) {
            throw new ApplicationException();
        }

        if ($rates !== null) {
            $rates = $this->rateRepository->transformJson($rates);
        }

        if ($reducedTickers !== null && $rates !== null) {
            $rates = $this->rateRepository->filterReduceByTickers(
                tickers: $reducedTickers,
                collection: $rates
            );
        }

        if ($rawRates) {
            if ($rates !== null) {
                $rates = $this->rateRepository->filterRates($rates);
            }

            if ($rates !== null) {
                $rates = $this->rateRepository->filterSortRates($rates);
            }
        }

        return $rates;
    }

    public function fetchRates(): string
    {
        return $this->rateRepository->fetchRates();
    }

    /**
     * @throws Throwable
     * @throws RedisException
     */
    public function saveRates(string $string): void
    {
        $this->rateRepository->saveRates($string);
    }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return ConvertDTO
     */
    public function convert(string $from, string $to, float $amount): ConvertDTO
    {
        if ($amount < self::MIN_AMOUNT) {
            throw new ApplicationException();
        }

        $rates = $this->getRates();

        $convertedValue = match (true) {
            (self::BTC === $from) => $this->convertFromBTC(rates: $rates, to: $to, amount: $amount),
            (self::BTC === $to) => $this->convertToBTC(rates: $rates, from: $from, amount: $amount),
            default => $this->convertFromTo(rates: $rates, from: $from, to: $to, amount: $amount),
        };

        $convertDTO = new ConvertDTO();
        $convertDTO->setConvertedValue($convertedValue);
        $convertDTO->setRate($this->rateRepository->calculateRate(
            before: (string)$amount,
            after: $convertedValue,
            precision: (self::BTC === $to) ? RateRepository::PRECISION_CRYPTO : RateRepository::PRECISION_FIAT
        ));

        return $convertDTO;
    }

    private function convertFromBTC(?Collection $rates, string $to, float $amount): string
    {
        if (! $rates?->offsetExists($to)) {
            throw new ApplicationException(); // 'Unsupported currency'
        }

        return $this->rateRepository->convertFromBTC(
            amount: (string) $amount,
            rate: (float) $rates?->offsetGet($to)
        );
    }

    private function convertToBTC(?Collection $rates, string $from, float $amount): string
    {
        if (! $rates?->offsetExists($from)) {
            throw new ApplicationException(); // 'Unsupported currency'
        }

        return $this->rateRepository->convertToBTC(
            amount: (string) $amount,
            rate: (float) $rates?->offsetGet($from)
        );
    }

    private function convertFromTo(?Collection $rates, string $from, string $to, float $amount): string
    {
        if (! $rates?->offsetExists($to) || ! $rates?->offsetExists($from)) {
            throw new ApplicationException(); // 'Unsupported currency'
        }

        $s = (float) $rates?->offsetGet($from);
        $d = (float) $rates?->offsetGet($to);

        if (! $s) {
            throw new ApplicationException();
        }

        return $this->rateRepository->convert($s, $d, $amount);
    }
}
