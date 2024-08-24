<?php

declare(strict_types=1);

namespace App\DataFilters;

use App\Services\FeeService;
use Illuminate\Support\Collection;

readonly class FeeRatesFilter implements DataFilterInterface
{
    public function __construct(private FeeService $feeService)
    {
    }

    public function apply(string|Collection $data): ?Collection
    {
        $new = [];
        foreach ($data as $item) {
            $new[$item['symbol']] = $this->feeService->calc((string) $item['last']);
        }

        return Collection::make($new);
    }
}
