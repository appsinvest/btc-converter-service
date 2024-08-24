<?php

namespace App\Services;

class FeeRepository
{
    public function calc(string $amount, float $fee): string
    {
        return bcmul((string) (1.00 + fdiv($fee, 100)), $amount, 2);
    }
}
