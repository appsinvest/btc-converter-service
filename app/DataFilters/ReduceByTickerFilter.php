<?php

declare(strict_types=1);

namespace App\DataFilters;

use Illuminate\Support\Collection;

class ReduceByTickerFilter implements DataFilterInterface
{
    private array $tickers = [];

    public function setTickers(array $tickers): void
    {
        $this->tickers = $tickers;
    }

    public function apply(string|Collection $data): ?Collection
    {
        return $data->reject(function ($rate, $key) {
            return ! in_array($key, $this->tickers, true);
        });
    }
}
