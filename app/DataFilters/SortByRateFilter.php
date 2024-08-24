<?php

declare(strict_types=1);

namespace App\DataFilters;

use Illuminate\Support\Collection;

class SortByRateFilter implements DataFilterInterface
{
    public function apply(string|Collection $data): ?Collection
    {
        return $data->sortDesc()->reverse();
    }
}
