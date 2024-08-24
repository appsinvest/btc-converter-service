<?php

declare(strict_types=1);

namespace App\DataFilters;

use App\Exceptions\ApplicationException;
use Illuminate\Support\Collection;
use Throwable;

class TransformJsonFilter implements DataFilterInterface
{
    public function apply(string|Collection $data): ?Collection
    {
        $array = null;
        try {
            $array = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new ApplicationException();
        }

        return $array !== null ? Collection::make($array) : null;
    }
}
