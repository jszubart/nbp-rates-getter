<?php

namespace App\Manager;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;

class CurrencyManager
{
    public function __construct(private readonly CurrencyRepository $currencyRepository)
    {
    }

    public function createCurrency(string $name, string $currencyCode, string $exchangeRate): void
    {
        $currency = new Currency();
        $currency->setName($name);
        $currency->setCurrencyCode($currencyCode);
        $currency->setExchangeRate($exchangeRate);

        $this->currencyRepository->add($currency, true);
    }

    public function updateCurrencyExchangeRate(Currency $currency, string $exchangeRate): void
    {
        $currency->setExchangeRate($exchangeRate);

        $this->currencyRepository->add($currency, true);
    }
}