<?php

namespace App\Services;

use App\Exceptions\ApplicationException;

readonly class FeeService
{
    public function __construct(private FeeRepository $feeRepository)
    {
    }

    public function calc(string $amount): string
    {
        $fee = (float) config('rates.fee');
        if ($fee <= 0) {
            throw new ApplicationException(); // Invalid fee rate
        }

        return $this->feeRepository->calc(amount: $amount, fee: $fee);
    }
}
