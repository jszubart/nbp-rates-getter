<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;

class CurrencyService
{
    public function __construct(private readonly CurrencyRepository $currencyRepository)
    {

    }

    public function getCurrencyByCode(string $currencyCode): ?Currency
    {
        return $this->currencyRepository->findOneBy([
            'currencyCode' => $currencyCode
        ]);
    }
}
