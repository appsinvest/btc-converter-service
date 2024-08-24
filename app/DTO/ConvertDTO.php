<?php

/**
 * ConvertDTO
 * php version 8.3
 *
 * @category DTO
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */

declare(strict_types=1);

namespace App\DTO;

use SoftInvest\DTO\DataTransferObject;

/**
 * ConvertDTO
 * php version 8.3
 *
 * @category DTO
 *
 * @author   appsinvest <appscenter@proton.me>
 * @license  GPLv3 License
 *
 * @link     https://github.com/appsinvest/btc-converter-service
 */
class ConvertDTO extends DataTransferObject
{
    protected ?string $convertedValue = null;
    protected ?string $rate = null;

    public function getConvertedValue(): ?string
    {
        return $this->convertedValue;
    }

    public function setConvertedValue(?string $convertedValue): void
    {
        $this->convertedValue = $convertedValue;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(?string $rate): void
    {
        $this->rate = $rate;
    }
}
