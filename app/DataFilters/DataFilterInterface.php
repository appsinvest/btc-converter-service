<?php

namespace App\DataFilters;

use Illuminate\Support\Collection;

interface DataFilterInterface
{
    public function apply(string|Collection $data): ?Collection;
}
